"""Check shared public navigation against isolated rendered fixtures; no live merchant data."""
from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode = True
from chrome_cdp import Chrome
project = Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='metohub-public-qa-') as folder:
    folder = Path(folder)
    subprocess.run(['php', str(project/'tests/browser/render_landing_fixture.php'), str(folder/'home.html'), '--public-pages'], cwd=project, check=True)
    for file in folder.glob('*.html'):
        html = re.sub(r'https?://127\.0\.0\.1:8000/', (project/'public').as_uri()+'/', file.read_text())
        file.write_text(html)
    with Chrome() as c:
        c.call('Page.addScriptToEvaluateOnNewDocument', {'source': "window.__pageErrors=[];window.addEventListener('error',e=>window.__pageErrors.push(e.message));"})
        for width in [375, 1440]:
            c.size(width)
            baseline = None
            for page in ['home', 'pricing', 'privacy-policy', 'terms', 'refund-cancellation', 'about', 'contact', 'themes', 'blogs', 'blogs-7']:
                c.visit((folder/(page+'.html')).as_uri())
                metrics = c.js("""(()=>{const h=document.querySelector('[data-marketing-header]'),f=document.querySelector('[data-marketing-footer]');const properties=e=>{const s=getComputedStyle(e),b=e.getBoundingClientRect();return {height:b.height,font:s.fontFamily,color:s.color,background:s.backgroundColor,padding:s.padding}};return {header:properties(h),footer:properties(f),overflow:document.documentElement.scrollWidth>innerWidth+1,errors:window.__pageErrors};})()""")
                assert not metrics['overflow'], (width,page,'horizontal overflow')
                assert not metrics['errors'], (width,page,metrics['errors'])
                if baseline is None: baseline=metrics
                else:
                    assert metrics['header']==baseline['header'], (width,page,'header mismatch',metrics['header'],baseline['header'])
                    assert metrics['footer']==baseline['footer'], (width,page,'footer mismatch',metrics['footer'],baseline['footer'])
                if page != 'home':
                    theme = c.js("""(()=>{const p=document.querySelector('.page-content'),s=getComputedStyle(p);return {primary:s.getPropertyValue('--primary').trim(),background:s.backgroundColor,font:s.fontFamily,heading:getComputedStyle(p.querySelector('h1,h2')).fontFamily};})()""")
                    assert theme['primary']=='#244a3b', (page,theme)
                    assert theme['background']=='rgb(251, 250, 247)', (page,theme)
                    assert 'Manrope' in theme['font'] and 'Manrope' in theme['heading'], (page,theme)
                if page == 'themes':
                    assert c.js("document.querySelectorAll('[data-public-theme]').length") == 15
                    assert c.js("Array.from(document.querySelectorAll('.public-theme-preview')).every(a=>a.href.includes('preview_theme='))")
                if page == 'blogs-7':
                    assert c.js("Boolean(document.querySelector('[aria-label=Breadcrumb]'))")
                    assert c.js("JSON.parse(document.querySelector('script[type=\"application/ld+json\"]').textContent)['@graph'].some(node=>node['@type']==='BlogPosting')")
                if page in ['home', 'pricing']:
                    assert c.js("document.querySelectorAll('#pricing .pricing-card').length") == 3
                    assert c.js("document.querySelectorAll('#pricing .price-show-yearly:not([hidden])').length") == 0
                    c.js("document.getElementById('btn-yearly').click()")
                    assert c.js("document.getElementById('btn-yearly').getAttribute('aria-pressed')") == 'true'
                    assert c.js("document.querySelectorAll('#pricing .price-show-monthly:not([hidden])').length") == 0
                    assert c.js("document.querySelector('#pricing .price-show-yearly').textContent.includes('1,089')")
                    c.js("document.getElementById('btn-monthly').click()")
                    assert c.js("document.querySelectorAll('#pricing .price-show-monthly:not([hidden])').length") == 3
                    c.js("document.getElementById('pricing').scrollIntoView({block:'start',behavior:'instant'})")
                    time.sleep(.15)
                    c.screenshot('/private/tmp/metohub-pricing-'+page+'-'+str(width)+'.png')
                if width==375:
                    c.js("document.getElementById('menuBtn').click()")
                    assert c.js("!document.getElementById('mm').hidden"), page
                    c.js("document.dispatchEvent(new KeyboardEvent('keydown',{key:'Escape'}))")
                    assert c.js("document.getElementById('mm').hidden"), page
                c.js('scrollTo(0,400)');time.sleep(.15)
                assert abs(c.js("document.querySelector('[data-marketing-header]').getBoundingClientRect().top"))<1, (width,page,'header not sticky')
                if page in ['themes','privacy-policy','about','contact','blogs-7']:
                    c.js('scrollTo(0,0)');time.sleep(.15)
                    c.screenshot('/private/tmp/metohub-shared-'+page+'-'+str(width)+'.png')
                print(width,page,'shared header/footer, navigation and sticky position pass',flush=True)
