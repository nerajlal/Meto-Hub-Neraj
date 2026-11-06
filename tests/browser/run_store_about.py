from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode=True
from chrome_cdp import Chrome
project=Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='store-about-qa-') as directory:
    folder=Path(directory)
    subprocess.run(['php',str(project/'tests/browser/render_landing_fixture.php'),str(folder/'landing.html'),'--store-themes'],cwd=project,check=True)
    with Chrome() as c:
        for theme in [1,2,7,30]:
            page=folder/f'template_{theme}-about.html'
            page.write_text(re.sub(r'https?://127\.0\.0\.1:8000/',(project/'public').as_uri()+'/',page.read_text()))
            c.visit(page.as_uri())
            for width in [375,1440]:
                c.size(width);time.sleep(.2)
                assert not c.js('document.documentElement.scrollWidth>innerWidth+1'),(theme,width)
                assert c.js("document.querySelectorAll('h1').length")==1
                assert c.js("document.querySelectorAll('.store-about-help a').length")==3
                assert c.js("!!document.querySelector('.store-about-brand')")
                if theme==2:
                    import base64
                    Path(f'/private/tmp/metohub-about-{width}.png').write_bytes(base64.b64decode(c.call('Page.captureScreenshot',{'format':'png'})['data']))
            print(f'PASS: About layout theme {theme}, mobile and desktop.',flush=True)
