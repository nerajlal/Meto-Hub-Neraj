"""Render real theme pages with isolated fixture data; no orders or mail sent."""
from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode = True
from chrome_cdp import Chrome
project = Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='metohub-themes-') as directory:
    folder = Path(directory)
    subprocess.run(['php', str(project/'tests/browser/render_landing_fixture.php'), str(folder/'landing.html'), '--store-themes'], cwd=project, check=True)
    for page in folder.glob('*.html'):
        page.write_text(re.sub(r'https?://127\.0\.0\.1:8000/', (project/'public').as_uri()+'/', page.read_text()))
    with Chrome() as c:
        for number in ([int(value) for value in sys.argv[1].split(',')] if len(sys.argv)>1 and ',' in sys.argv[1] else range(int(sys.argv[1]) if len(sys.argv)>1 else 16,32)):
            for name in ['home','catalog','product']:
                c.visit((folder/f'template_{number}-{name}.html').as_uri())
                for width in [375,1440]:
                    c.size(width);time.sleep(.1)
                    assert not c.js('document.documentElement.scrollWidth>innerWidth+1'), (number,name,width,c.js('document.documentElement.scrollWidth'))
                    assert c.js('document.querySelectorAll("h1").length') == 1,(number,name)
                    if number==31 and name=='home':
                        assert c.js("[...document.querySelectorAll('.sf-product-pantryselect')].some(card=>card.querySelectorAll('[data-market-variant]').length===1 && card.querySelector('[data-market-variant]').getAttribute('aria-pressed')==='true' && getComputedStyle(card.querySelector('.sf-product-size')).display==='none')")
                        alignment=c.js("(()=>{const cards=[...document.querySelector('.sf-product-pantryselect').closest('.product-grid').querySelectorAll('.sf-product-pantryselect')];return cards.slice(0,2).map(card=>({cardHeight:card.getBoundingClientRect().height,captionHeight:card.querySelector('.ps-card-caption').getBoundingClientRect().height,captionFlex:getComputedStyle(card.querySelector('.ps-card-caption')).flex,display:getComputedStyle(card).display,align:getComputedStyle(card).alignSelf,price:card.querySelector('.sf-product-price').getBoundingClientRect().top,add:card.querySelector('.inline-add-btn').getBoundingClientRect().top,variants:card.querySelector('.sf-market-variants').getBoundingClientRect().top}));})()")
                        assert len(alignment)==2 and abs(alignment[0]['price']-alignment[1]['price'])<1 and abs(alignment[0]['add']-alignment[1]['add'])<1 and abs(alignment[0]['variants']-alignment[1]['variants'])<1,(width,alignment)

                        assert c.js("document.querySelectorAll('.ps-category').length")>0
                        assert c.js("document.querySelectorAll('.ps-trust-row[aria-hidden=true]').length")==3
                        assert c.js("document.querySelectorAll('.ps-trust-row:not([aria-hidden]) .ps-trust-icon img').length")>0
                        c.js("document.querySelector('.ps-trust-strip').scrollIntoView({block:'center'})");time.sleep(.25)
                        assert c.js("document.querySelector('.ps-trust-icon img').naturalWidth")>0
                        assert c.js("getComputedStyle(document.querySelector('.ps-trust-strip')).backgroundColor")=='rgba(0, 0, 0, 0)'
                        c.js("document.querySelector('.ps-trust-strip').scrollIntoView({block:'center'})");time.sleep(.1)
                        c.screenshot(f'/private/tmp/pantry-trust-{width}.png')
                        c.js('scrollTo(0,0)')
                        assert c.js("getComputedStyle(document.querySelector('.ps-trust-track')).animationName")=='ps-trust-scroll'
                        c.js("document.querySelector('.ps-trust-strip').focus()")
                        assert c.js("getComputedStyle(document.querySelector('.ps-trust-track')).animationPlayState")=='paused'
                        c.js('document.activeElement.blur()')
                        assert c.js("document.querySelectorAll('.ps-bundle-promo').length")==4
                        assert c.js("[...document.querySelectorAll('.ps-bundle-promo')].some(e=>getComputedStyle(e).backgroundImage.startsWith('linear-gradient(90deg'))")
                        assert c.js("[...document.querySelectorAll('.ps-bundle-promo')].some(e=>getComputedStyle(e).backgroundColor==='rgb(24, 58, 20)' && getComputedStyle(e).color==='rgb(255, 255, 255)')")
                        c.js("document.querySelector('.ps-bundle-shelf').scrollIntoView()");time.sleep(.3)
                        assert c.js("document.querySelector('.ps-bundle-image img').naturalWidth")>0
                        c.js('scrollTo(0,0)')
                        assert c.js("document.querySelector('.ps-bundle-rail').scrollWidth>document.querySelector('.ps-bundle-rail').clientWidth")
                        c.js("window.bundlePromoCalls=[];window.updateInlineCart=(key,delta)=>bundlePromoCalls.push({key,delta});document.querySelector('.ps-bundle-promo .inline-add-btn').click()")
                        assert c.js("bundlePromoCalls[0].key.startsWith('bundle-') && bundlePromoCalls[0].delta===1")
                        assert c.js("getComputedStyle(document.querySelector('.sf-product-pantryselect .card-info')).paddingLeft")=='0px'
                        assert c.js("document.querySelectorAll('[data-slide-dot]').length")==2
                        c.js("document.querySelector('.sf-product-pantryselect [data-market-variant]:nth-child(2)').click()")
                        assert c.js("document.querySelector('.sf-product-pantryselect .p-price').textContent").endswith('799.00')
                        assert c.js("document.querySelector('.sf-product-pantryselect .product-action-wrapper').dataset.cartKey").endswith('-Large-Pack')
                    if number==21 and name in ['home','catalog'] and width==375:
                        assert c.js('document.querySelector(".sf-product").getBoundingClientRect().width')>300, 'Harvest cards must be full-width shopping rows'
                    c.js('scrollTo(0,700)');time.sleep(.05)
                    assert abs(c.js('document.querySelector(".sf-header-row").getBoundingClientRect().top')) < 1,(number,name,width)
                    if name=='product' and width==375:
                        assert abs(c.js('document.querySelector("#main-product-actions").getBoundingClientRect().bottom-innerHeight')) < 2,(number,'purchase bar')
                    if name=='home':
                        c.js('scrollTo(0,0)');time.sleep(.1)
                        c.screenshot(f'/private/tmp/metohub-template-{number}-{width}.png')
                    if number==31:
                        c.js("document.querySelector('.ps-bundle-shelf').scrollIntoView()" if name=='home' else 'scrollTo(0,0)')
                        c.screenshot(f'/private/tmp/metohub-template-31-{name}-detail-{width}.png')
                    c.js('scrollTo(0,0)')
            print(f'PASS template_{number}: home/catalog/product, mobile/desktop, sticky header and purchase bar.',flush=True)
