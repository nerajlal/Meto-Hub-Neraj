from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode=True
from chrome_cdp import Chrome
project=Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='header-widths-') as directory:
    folder=Path(directory)
    subprocess.run(['php',str(project/'tests/browser/render_landing_fixture.php'),str(folder/'landing.html'),'--store-themes'],cwd=project,check=True)
    with Chrome() as c:
        for number in range(2,32):
            page=folder/f'template_{number}-home.html'
            page.write_text(re.sub(r'https?://127\.0\.0\.1:8000/',(project/'public').as_uri()+'/',page.read_text()))
            c.visit(page.as_uri());c.size(1920);c.js('scrollTo(0,600)');time.sleep(.05)
            bounds=c.js('(()=>{const e=document.querySelector(".sf-header>.sf-header-row,.sf-header>.sf-pantry-masthead"),r=e.getBoundingClientRect();return {left:r.left,right:r.right,width:document.documentElement.clientWidth,position:getComputedStyle(e).position};})()')
            assert abs(bounds['left'])<1 and abs(bounds['right']-bounds['width'])<1,(number,bounds)
            assert bounds['position']=='fixed',(number,bounds)
            assert not c.js('document.documentElement.scrollWidth>innerWidth+1'),number
            print(f'PASS template_{number}: fixed header covers wide viewport.',flush=True)
