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
                assert c.js("document.querySelector('.offer-collection-heading img').naturalWidth")>0,c.js("document.querySelector('.offer-collection-heading img').src")
                assert c.js("document.querySelectorAll('[data-pool-offer]').length")==1
                assert c.js("document.querySelectorAll('[data-pool-offer] .product-card').length")>0
                assert not c.js("!!document.querySelector('[data-pool-offer] [data-cart-key^=\"bundle-\"]')")
                if theme==31:
                    assert c.js("document.querySelectorAll('.ps-bundle-promo').length")==4
                    c.screenshot(f'/private/tmp/offer-collection-{width}.png')
            print(f'PASS: theme {theme} automatic offer collection, cover image, pools and mobile/desktop sizing.',flush=True)
