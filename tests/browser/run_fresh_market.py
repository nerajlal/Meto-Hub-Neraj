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
        for brand, foreground, expected in [('#48a51c','#000000','rgb(72, 165, 28)'),('#183a14','#ffffff','rgb(24, 58, 20)')]:
            c.js(f"document.documentElement.style.setProperty('--accent-color','{brand}');document.documentElement.style.setProperty('--store-brand-fg','{foreground}');document.documentElement.style.setProperty('--primary-color','{brand}');document.documentElement.style.setProperty('--store-stock-fg','{foreground}')")
            time.sleep(.5)
            for selector in ['.sf-market-topline','.sf-footer-market']:
                assert c.js(f"getComputedStyle(document.querySelector('{selector}')).backgroundColor")==expected
                assert c.js(f"getComputedStyle(document.querySelector('{selector} a')).color")==('rgb(0, 0, 0)' if foreground=='#000000' else 'rgb(255, 255, 255)'), (selector, c.js(f"getComputedStyle(document.querySelector('{selector} a')).color"))
        c.js("document.documentElement.style.setProperty('--accent-color','#48a51c');document.documentElement.style.setProperty('--primary-color','#183a14')")
        time.sleep(.5)
        assert c.js("getComputedStyle(document.querySelector('.sf-market-topline')).backgroundColor")=='rgb(72, 165, 28)'
        assert c.js("getComputedStyle(document.querySelector('.sf-footer-market')).backgroundColor")=='rgb(24, 58, 20)'
        current = "document.querySelector('[data-slide-dot][aria-current=true]').dataset.slideDot"
        assert c.js("document.querySelectorAll('[data-slide-dot]').length")==2
        assert not c.js("!!document.querySelector('[data-slide-next],[data-slide-prev],[data-slide-pause],[data-slide-status]')")
        assert c.js(current)=='0'
        c.js("document.querySelector('[data-slide-dot=\"1\"]').click()")
        assert c.js(current)=='1'
        c.js("document.querySelector('[data-slide-dot=\"0\"]').click();document.activeElement.blur()")
        c.call('Input.dispatchMouseEvent',{'type':'mouseMoved','x':0,'y':0})
        time.sleep(5.3)
        assert c.js(current)=='1'
        assert c.js("document.querySelectorAll('.sf-merchant-banner:not([hidden])').length")==1
        c.call('Emulation.setEmulatedMedia',{'features':[{'name':'prefers-reduced-motion','value':'reduce'}]})
        time.sleep(.15)
        before=c.js(current)
        time.sleep(5.2)
        assert c.js(current)==before
        c.js("document.querySelector('[data-slide-dot=\"0\"]').click()")
        assert c.js(current)=='0'
        assert c.js("!!document.querySelector('.sf-market-story a[href*=\"all-products\"]')")
        print('PASS: Fresh Market autoplay, clickable dots, reduced motion, mobile/desktop sizing and pre-footer links.',flush=True)
