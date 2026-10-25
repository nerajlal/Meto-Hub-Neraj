"""Exercise Git/push guards against temporary local repositories, never GitHub."""
import os
from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[2]


class CliTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory()
        self.addCleanup(self.tmp.cleanup)
        self.root = Path(self.tmp.name)
        self.repo = self.root/'repo'
        self.repo.mkdir()
        self.git('init', '-q', '-b', 'main')
        self.git('config', 'user.name', 'Deployment Test')
        self.git('config', 'user.email', 'deploy-test@example.invalid')
        shutil.copy(ROOT/'deploy.sh', self.repo/'deploy.sh')
        self.git('add', 'deploy.sh')
        self.git('commit', '-qm', 'fixture')
        self.remote = self.root/'origin.git'
        subprocess.run(['git', 'init', '--bare', '-q', str(self.remote)], check=True)
        self.git('remote', 'add', 'origin', str(self.remote))
        bin_dir = self.root/'bin'
        bin_dir.mkdir()
        ssh = bin_dir/'ssh'
        ssh.write_text('#!/bin/sh\nexit 77\n')
        ssh.chmod(0o755)
        self.env = dict(os.environ, PATH=str(bin_dir)+':'+os.environ['PATH'])

    def git(self, *args):
        return subprocess.check_output(['git', *args], cwd=self.repo, stderr=subprocess.STDOUT, text=True).strip()

    def run_cli(self, *args):
        return subprocess.run(['bash', 'deploy.sh', *args], cwd=self.repo, env=self.env, text=True, capture_output=True)

    def test_dirty_tree_rejected_before_push(self):
        (self.repo/'uncommitted.txt').write_text('work')
        result = self.run_cli('test', '--push')
        self.assertEqual(result.returncode, 1)
        self.assertIn('clean working tree', result.stdout)

    def test_push_requires_deployment_branch(self):
        self.git('checkout', '-qb', 'feature')
        result = self.run_cli('test', '--push')
        self.assertEqual(result.returncode, 1)
        self.assertIn('current branch to be main', result.stdout)

    def test_push_uploads_commit_to_local_origin_before_ssh(self):
        result = self.run_cli('test', '--push')
        self.assertEqual(result.returncode, 77)  # Intentional mock SSH stop, after successful push.
        remote_sha = subprocess.check_output(['git', '--git-dir', str(self.remote), 'rev-parse', 'refs/heads/main'], text=True).strip()
        self.assertEqual(remote_sha, self.git('rev-parse', 'HEAD'))

    def test_unpushed_commit_rejected(self):
        self.git('push', '-q', 'origin', 'main')
        (self.repo/'change.txt').write_text('new commit')
        self.git('add', 'change.txt')
        self.git('commit', '-qm', 'unpublished')
        result = self.run_cli('test')
        self.assertEqual(result.returncode, 1)
        self.assertIn('not pushed', result.stdout)

    def test_dry_run_never_pushes_or_connects(self):
        result = self.run_cli('prod', '--push', '--dry-run')
        self.assertEqual(result.returncode, 0)
        self.assertIn('domain: https://metohub.cloud', result.stdout)
        refs = subprocess.check_output(['git', '--git-dir', str(self.remote), 'for-each-ref'], text=True)
        self.assertEqual(refs, '')


if __name__ == '__main__':
    unittest.main()
