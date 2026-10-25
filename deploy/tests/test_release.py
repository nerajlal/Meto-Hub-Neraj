"""Offline orchestration tests: no SSH, Docker daemon, credentials or live changes."""
import io
import json
import os
from pathlib import Path
import subprocess
import tarfile
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[2]
SHA = 'a' * 40
MOCK = r'''#!/usr/bin/env python3
import json,os,sys
from pathlib import Path
name=Path(sys.argv[0]).name
args=sys.argv[1:]
with open(os.environ['MOCK_LOG'],'a') as f: f.write(json.dumps([name,*args])+'\n')
s=' '.join(args)
if name=='id': print('0')
elif name=='docker':
 if 'config --format json' in s:
  project=os.environ['PROJECT']
  print(json.dumps({'name':project,'services':{'web':{'ports':[{'host_ip':'127.0.0.1','published':os.environ['PORT']}]}},'volumes':{k:{'name':project+'_'+k} for k in ['db','uploads','private','logs']}}))
 elif 'ps -q' in s: print('container-id')
 elif args and args[0]=='inspect': print('true')
 elif 'mysqldump' in s:
  if os.environ.get('FAIL')=='backup': sys.exit(1)
  print('-- SQL backup')
 elif 'tar -czf' in s: print('mock upload backup')
 elif 'artisan migrate --force' in s and os.environ.get('FAIL')=='migration': sys.exit(1)
 elif 'artisan app:preflight' in s and os.environ.get('FAIL')=='preflight': sys.exit(1)
elif name=='curl' and os.environ.get('FAIL')=='health': sys.exit(22)
'''


class ReleaseTests(unittest.TestCase):
    def scenario(self, environment='test', fail='', tested=True, action=None):
        tmp = tempfile.TemporaryDirectory()
        self.addCleanup(tmp.cleanup)
        root = Path(tmp.name)
        main, test, shared = root/'main', root/'test', root/'locks'
        base = test if environment == 'test' else main
        for folder in [base/'runtime', base/'shared', base/'incoming/upload.fixture', test/'deployment']:
            folder.mkdir(parents=True, exist_ok=True)
        (base/'runtime/.env').write_text('RELEASE=snapshot-before\n')
        (base/'runtime/compose.yml').write_text('# server-owned infrastructure\n')
        for name in ['app.env', 'db.env']:
            (base/'shared'/name).write_text('EXISTING=preserved\n')
        if tested:
            (test/'deployment/tested').write_text(SHA+'\n')
        archive = base/'incoming/upload.fixture/source.tar.gz'
        with tarfile.open(archive, 'w:gz') as tar:
            for name in ['deploy/Dockerfile', 'deploy/check-runtime.php']:
                data = b'fixture'
                entry = tarfile.TarInfo(name)
                entry.size = len(data)
                tar.addfile(entry, io.BytesIO(data))
        script = (ROOT/'deploy/release.sh').read_text()
        for original, replacement in [('/srv/apps/metohub-next-staging', test), ('/srv/apps/metohub-next-deploy', shared), ('/srv/apps/metohub-next', main)]:
            script = script.replace(original, str(replacement))
        controller = root/'release.sh'
        controller.write_text(script)
        bin_dir = root/'bin'
        bin_dir.mkdir()
        for name in ['docker', 'curl', 'id', 'flock']:
            p = bin_dir/name
            p.write_text(MOCK)
            p.chmod(0o755)
        log = root/'commands.jsonl'
        env = dict(os.environ, PATH=str(bin_dir)+':'+os.environ['PATH'], MOCK_LOG=str(log), FAIL=fail,
                   PROJECT='metohub-next-staging' if environment == 'test' else 'metohub-next-test',
                   PORT='8893' if environment == 'test' else '8892')
        if action == 'rollback':
            (base/'deployment').mkdir(exist_ok=True)
            (base/'deployment/previous').write_text('snapshot-older\n')
        result = subprocess.run(['bash', str(controller), action or environment, environment, SHA, str(archive.parent)], env=env, text=True, capture_output=True)
        commands = [json.loads(line) for line in log.read_text().splitlines()]
        return result, base, commands

    def test_success_backs_up_before_migration_and_preserves_config(self):
        result, base, commands = self.scenario()
        self.assertEqual(result.returncode, 0, result.stderr+result.stdout)
        self.assertEqual((base/'runtime/.env').read_text(), f'RELEASE={SHA}\n')
        self.assertEqual((base/'deployment/previous').read_text().strip(), 'snapshot-before')
        self.assertEqual((base/'deployment/tested').read_text().strip(), SHA)
        self.assertEqual((base/'shared/app.env').read_text(), 'EXISTING=preserved\n')
        self.assertEqual((base/'runtime/compose.yml').read_text(), '# server-owned infrastructure\n')
        calls = [' '.join(c) for c in commands]
        backup = next(i for i,c in enumerate(calls) if 'mysqldump' in c)
        migrate = next(i for i,c in enumerate(calls) if 'artisan migrate --force' in c)
        self.assertLess(backup, migrate)
        self.assertTrue(list((base/'deployment/backups').glob('*/database.sql')))

    def test_failure_retains_old_release_and_stops_public_services(self):
        for failure in ['backup', 'migration', 'health']:
            with self.subTest(failure=failure):
                result, base, commands = self.scenario(fail=failure)
                self.assertNotEqual(result.returncode, 0)
                self.assertEqual((base/'runtime/.env').read_text(), 'RELEASE=snapshot-before\n')
                self.assertEqual((base/'deployment/recovery-release').read_text().strip(), 'snapshot-before')
                self.assertIn('stop web worker scheduler', ' '.join(' '.join(c) for c in commands))

    def test_production_requires_test_marker_without_disruption(self):
        result, _, commands = self.scenario(environment='prod', tested=False)
        self.assertNotEqual(result.returncode, 0)
        self.assertFalse(any('stop' in c for c in commands))

    def test_production_promotes_without_rebuilding(self):
        result, _, commands = self.scenario(environment='prod')
        self.assertEqual(result.returncode, 0, result.stdout+result.stderr)
        self.assertFalse(any(c[:2] == ['docker', 'build'] for c in commands))

    def test_production_configuration_failure_does_not_stop_site(self):
        result, _, commands = self.scenario(environment='prod', fail='preflight')
        self.assertNotEqual(result.returncode, 0)
        self.assertFalse(any('stop' in c for c in commands))

    def test_rollback_does_not_run_migrations(self):
        result, base, commands = self.scenario(action='rollback')
        self.assertEqual(result.returncode, 0, result.stdout+result.stderr)
        self.assertEqual((base/'runtime/.env').read_text(), 'RELEASE=snapshot-older\n')
        self.assertFalse(any('artisan migrate --force' in ' '.join(c) for c in commands))
        self.assertFalse((base/'deployment/tested').exists())


if __name__ == '__main__':
    unittest.main()
