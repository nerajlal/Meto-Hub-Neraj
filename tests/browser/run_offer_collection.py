from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode=True
from chrome_cdp import Chrome
project=Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='offer-collection-qa-') as directory:
    folder=Path(directory)
    subprocess.run(['php',str(project/'tests/browser/render_landing_fixture.php'),str(folder/'landing.html'),'--store-themes'],cwd=project,check=True)
    with Chrome() as c:
        for theme in [1,2,31]:
            page=folder/f'template_{theme}-offers.html'
            html=page.read_text().replace('/storage/images/landing/','/images/landing/').replace('src="/images/', 'src="'+(project/'public').as_uri()+'/images/')
            page.write_text(re.sub(r'https?://127\.0\.0\.1:8000/',(project/'public').as_uri()+'/',html))
            c.visit(page.as_uri())
            for width in [375,1440]:
                c.size(width);time.sleep(.2)
                assert not c.js('document.documentElement.scrollWidth>innerWidth+1'),(theme,width)
                assert c.js('document.querySelectorAll("h1").length')==1
                assert c.js("document.querySelectorAll('[data-offer-collection]').length")==1
                button=c.js("(()=>{const button=document.querySelector('.offer-fixed .inline-add-btn'),r=button.getBoundingClientRect();return {width:r.width,height:r.height,bg:getComputedStyle(button).backgroundColor,brand:getComputedStyle(document.documentElement).getPropertyValue('--accent-color').trim()};})()")
                assert abs(button['width']-44)<1 and abs(button['height']-44)<1,(theme,width,button)
                assert c.js("(()=>{const b=document.querySelector('.offer-fixed .inline-add-btn'),p=document.createElement('span');p.style.background='var(--accent-color)';document.body.append(p);const same=getComputedStyle(b).backgroundColor===getComputedStyle(p).backgroundColor;p.remove();return same;})()")
                c.js("window.bundleButtonCalls=[];window.updateInlineCart=(key,delta)=>bundleButtonCalls.push({key,delta});document.querySelector('.offer-fixed .inline-add-btn').click()")
                assert c.js("bundleButtonCalls[0].key.startsWith('bundle-') && bundleButtonCalls[0].delta===1")

                assert c.js("document.querySelector('.offer-collection-heading img').naturalWidth")>0,c.js("document.querySelector('.offer-collection-heading img').src")
                assert c.js("document.querySelectorAll('[data-pool-offer]').length")==1
                assert c.js("document.querySelectorAll('[data-pool-offer] .product-card').length")>0
                assert not c.js("!!document.querySelector('[data-pool-offer] [data-cart-key^=\"bundle-\"]')")
                if theme==31:
                    assert c.js("document.querySelectorAll('.offer-fixed .product-card').length")==4
                    assert not c.js("!!document.querySelector('.ps-bundle-promo')")
                    c.screenshot(f'/private/tmp/offer-collection-{width}.png')
            print(f'PASS: theme {theme} automatic offer collection, cover image, pools and mobile/desktop sizing.',flush=True)
