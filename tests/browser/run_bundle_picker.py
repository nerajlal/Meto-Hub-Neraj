from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode=True
from chrome_cdp import Chrome
project=Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='bundle-picker-qa-') as directory:
    with Chrome() as c:
        for mode in ['bundle-create','bundle-edit']:
            page=Path(directory)/(mode+'.html')
            subprocess.run(['php',str(project/'tests/browser/render_settings_fixture.php'),str(page),'--'+mode],cwd=project,check=True)
            page.write_text(re.sub(r'https?://127\.0\.0\.1:8000/',(project/'public').as_uri()+'/',page.read_text()))
            c.visit(page.as_uri())
            for width in [375,1440]:
                c.size(width);time.sleep(.15)
                assert not c.js('document.documentElement.scrollWidth>innerWidth+1'),(mode,width)
            assert c.js("!!document.querySelector('[data-bundle-background]')")
            c.js("(()=>{const m=document.querySelector('[name=\"card_background[mode]\"]');m.value='solid';m.dispatchEvent(new Event('change'));const color=document.querySelector('[name=\"card_background[color]\"]');color.value='#183a14';color.dispatchEvent(new Event('input'))})()")
            assert c.js("getComputedStyle(document.querySelector('[data-background-preview]')).backgroundColor")=='rgb(24, 58, 20)'
            assert c.js("getComputedStyle(document.querySelector('[data-background-preview]')).color")=='rgb(255, 255, 255)'
            c.js("(()=>{const m=document.querySelector('[name=\"card_background[mode]\"]');m.value='gradient';m.dispatchEvent(new Event('change'))})()")
            assert 'linear-gradient' in c.js("document.querySelector('[data-background-preview]').style.background")
            c.js("(()=>{const m=document.querySelector('[name=\"card_background[mode]\"]');m.value='auto';m.dispatchEvent(new Event('change'))})()")
            assert c.js("document.querySelector('[data-background-colors]').hidden")
            if mode=='bundle-edit':
                assert c.js("document.querySelector('[name=\"variant_ids[]\"]').selectedOptions[0].textContent").startswith('250g')
                assert c.js("document.querySelector('[name=\"quantities[]\"]').value")=='2'
                assert c.js("document.getElementById('summary_total').textContent").endswith('350.00')
                c.js("document.querySelector('[data-remove]').click()")
            c.js("(()=>{const s=document.getElementById('bundle-product-search');s.value='Chips';s.dispatchEvent(new Event('input'));document.querySelector('[data-bundle-results] button').click()})()")
            assert c.js("document.querySelector('[data-title]').textContent")=='Chips <special>'
            assert c.js("document.querySelector('[name=\"variant_ids[]\"]').value")==''
            c.js("(()=>{const s=document.querySelector('[name=\"variant_ids[]\"]');s.selectedIndex=2;s.dispatchEvent(new Event('change'));const q=document.querySelector('[name=\"quantities[]\"]');q.value=3;q.dispatchEvent(new Event('input'))})()")
            expected='600.00' if mode=='bundle-create' else '550.00'
            assert c.js("document.getElementById('summary_total').textContent").endswith(expected)
            c.js("(()=>{const s=document.getElementById('bundle-product-search');s.value='Chips';s.dispatchEvent(new Event('input'));document.querySelector('[data-bundle-results] button').click()})()")
            assert c.js("document.querySelectorAll('[name=\"products[]\"]').length")==2
            print(f'PASS: {mode} variant choice, prices, quantities, duplicate product rows and responsive layout.',flush=True)
