#!/usr/bin/env python3
"""Run the real push worker in headless Chrome with browser APIs mocked."""
import argparse
import json
from pathlib import Path
import shutil
import subprocess
import tempfile

parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('--browser', help='Path to Chrome/Chromium')
args = parser.parse_args()
project = Path(__file__).resolve().parents[2]
candidates = [args.browser, shutil.which('chromium'), shutil.which('google-chrome'),
              '/Applications/Chrome.app/Contents/MacOS/Google Chrome',
              '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome']
browser = next((path for path in candidates if path and Path(path).is_file()), None)
if not browser:
    parser.error('Chrome/Chromium not found; pass --browser /path/to/browser')
source = (project / 'public/merchant-push-worker.js').read_text()
harness = (project / 'tests/browser/merchant-push-worker.js').read_text()
with tempfile.TemporaryDirectory(prefix='metohub-tracking-test-') as temporary:
    folder = Path(temporary)
    page = folder / 'test.html'
    encoded = json.dumps(source).replace('<', '\\u003c')
    page.write_text('<!doctype html><meta charset="utf-8"><body>RUNNING<script>'
                    'globalThis.merchantWorkerSource=' + encoded + ';</script><script>try{'
                    + harness + '}catch(error){document.body.textContent="FAIL: "+error.stack;}</script>')
    result = subprocess.run([browser, '--headless', '--disable-gpu', '--disable-background-networking',
                             '--no-first-run', '--user-data-dir=' + str(folder / 'profile'),
                             '--dump-dom', page.as_uri()], capture_output=True, text=True, timeout=30)
    if result.returncode != 0 or 'PASS: worker' not in result.stdout or 'FAIL:' in result.stdout:
        print(result.stdout or result.stderr)
        raise SystemExit(1)
    print('PASS: worker privacy, authenticated order links, safe click targets and foreground refresh')
