from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode=True
from chrome_cdp import Chrome
project=Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='fresh-market-qa-') as directory:
    folder=Path(directory)
    subprocess.run(['php',str(project/'tests/browser/render_landing_fixture.php'),str(folder/'landing.html'),'--store-themes'],cwd=project,check=True)
    page=folder/'template_2-home.html'
    page.write_text(re.sub(r'https?://127\.0\.0\.1:8000/',(project/'public').as_uri()+'/',page.read_text()))
    with Chrome() as c:
        c.visit(page.as_uri())
        for width in [375,1440,1920]:
            c.size(width);time.sleep(.1)
            assert not c.js('document.documentElement.scrollWidth>innerWidth+1'),width
            c.js('scrollTo(0,600)')
            bounds=c.js('(()=>{const r=document.querySelector(".sf-header-market>.sf-header-row").getBoundingClientRect();return {left:r.left,right:r.right,width:document.documentElement.clientWidth};})()')
            assert abs(bounds['left'])<1 and abs(bounds['right']-bounds['width'])<1, (width,bounds)
            c.js('scrollTo(0,0)')
        assert c.js("document.querySelector('[data-slide-status]').textContent")=='1 / 2'
        c.js("document.querySelector('[data-slide-next]').click()")
        assert c.js("document.querySelector('[data-slide-status]').textContent")=='2 / 2'
        c.js("document.querySelector('[data-slide-next]').click()")
        assert c.js("document.querySelector('[data-slide-status]').textContent")=='1 / 2'
        c.js("document.querySelector('[data-slide-pause]').click();document.activeElement.blur()")
        time.sleep(5.2)
        assert c.js("document.querySelector('[data-slide-status]').textContent")=='1 / 2'
        c.js("document.querySelector('[data-slide-pause]').click();document.activeElement.blur()")
        c.call('Input.dispatchMouseEvent',{'type':'mouseMoved','x':0,'y':0})
        time.sleep(5.3)
        assert c.js("document.querySelector('[data-slide-status]').textContent")=='2 / 2'
        assert c.js("document.querySelectorAll('.sf-merchant-banner:not([hidden])').length")==1
        c.call('Emulation.setEmulatedMedia',{'features':[{'name':'prefers-reduced-motion','value':'reduce'}]})
        time.sleep(.15)
        assert c.js("document.querySelector('[data-slide-pause]').textContent")=='Play'
        assert c.js("!!document.querySelector('.sf-market-story a[href*=\"all-products\"]')")
        print('PASS: Fresh Market autoplay, wraparound, pause, reduced motion, mobile/desktop sizing and pre-footer links.',flush=True)
