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
        for number in range(int(sys.argv[1]) if len(sys.argv)>1 else 16,31):
            for name in ['home','catalog','product']:
                c.visit((folder/f'template_{number}-{name}.html').as_uri())
                for width in [375,1440]:
                    c.size(width);time.sleep(.1)
                    assert not c.js('document.documentElement.scrollWidth>innerWidth+1'), (number,name,width,c.js('document.documentElement.scrollWidth'))
                    assert c.js('document.querySelectorAll("h1").length') == 1,(number,name)
                    c.js('scrollTo(0,700)');time.sleep(.05)
                    assert abs(c.js('document.querySelector(".sf-header-row").getBoundingClientRect().top')) < 1,(number,name,width)
                    if name=='product' and width==375:
                        assert abs(c.js('document.querySelector("#main-product-actions").getBoundingClientRect().bottom-innerHeight')) < 2,(number,'purchase bar')
                    if name=='home':
                        c.js('scrollTo(0,0)');time.sleep(.1)
                        c.screenshot(f'/private/tmp/metohub-template-{number}-{width}.png')
                    c.js('scrollTo(0,0)')
            print(f'PASS template_{number}: home/catalog/product, mobile/desktop, sticky header and purchase bar.',flush=True)
