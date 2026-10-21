"""Visual checks of expired Billing and public unavailability; no provider interactions."""
from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode = True
from chrome_cdp import Chrome

project = Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='metohub-expiry-qa-') as folder:
    for mode in ['expired', 'unavailable']:
        file = Path(folder)/(mode+'.html')
        subprocess.run(['php', str(project/'tests/browser/render_settings_fixture.php'), str(file), '--'+mode], cwd=project, check=True)
        file.write_text(re.sub(r'https?://127\.0\.0\.1:8000/', (project/'public').as_uri()+'/', file.read_text()))
    with Chrome() as c:
        for mode in ['expired', 'unavailable']:
            c.visit((Path(folder)/(mode+'.html')).as_uri())
            for width in [320, 375, 768, 1440]:
                c.size(width)
                time.sleep(.2)
                assert not c.js('document.documentElement.scrollWidth>innerWidth+1'), (mode,width,'overflow')
                if mode == 'expired':
                    assert c.js("document.querySelectorAll('#adminSidebar a').length") == 1
                    assert c.js("document.querySelector('#adminSidebar a').textContent.trim()") == 'Billing'
                    assert c.js("document.querySelectorAll('.subscribe-btn').length") == 3
                    assert not c.js("Boolean(document.querySelector('#merchant-notifications-config'))")
                else:
                    assert c.js("document.querySelector('h1').textContent") == 'Store temporarily unavailable'
                c.screenshot('/private/tmp/metohub-'+mode+'-'+str(width)+'.png')
                print(mode, width, 'fits; expected subscription access screen present', flush=True)
