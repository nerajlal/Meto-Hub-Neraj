"""Read-only layout checks with isolated merchant data; no live login or mutations."""
from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode = True
from chrome_cdp import Chrome

project = Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='metohub-settings-qa-') as folder:
    fixture = Path(folder)/'settings.html'
    subprocess.run(['php', str(project/'tests/browser/render_settings_fixture.php'), str(fixture)], cwd=project, check=True)
    fixture.write_text(re.sub(r'https?://127\.0\.0\.1:8000/', (project/'public').as_uri()+'/', fixture.read_text()))
    with Chrome() as c:
        c.call('Page.addScriptToEvaluateOnNewDocument', {'source': "window.__errors=[];addEventListener('error',e=>__errors.push(e.message));"})
        c.visit(fixture.as_uri())
        for width in [320, 375, 768, 1440]:
            c.size(width)
            time.sleep(.2)
            assert not c.js('document.documentElement.scrollWidth>innerWidth+1'), (width, 'page overflow')
            assert not c.js('window.__errors'), (width, c.js('window.__errors'))
            assert c.js("document.querySelectorAll('.settings-overview .settings-row').length") == 21
            assert c.js("document.querySelector('.settings-overview').textContent.includes('Paused in GTM mode')")
            assert c.js("Array.from(document.querySelectorAll('.settings-overview a')).every(a=>a.getAttribute('href') && !a.getAttribute('href').startsWith('javascript:'))")
            c.call('Input.dispatchKeyEvent', {'type': 'keyDown', 'key': 'Tab', 'code': 'Tab', 'windowsVirtualKeyCode': 9})
            c.call('Input.dispatchKeyEvent', {'type': 'keyUp', 'key': 'Tab', 'code': 'Tab', 'windowsVirtualKeyCode': 9})
            c.js("document.querySelector('.settings-overview a').focus()")
            assert c.js("getComputedStyle(document.activeElement).outlineStyle") != 'none'
            c.screenshot('/private/tmp/metohub-settings-'+str(width)+'.png')
            print(width, 'Settings fits; navigation, recorded statuses and keyboard focus pass', flush=True)
