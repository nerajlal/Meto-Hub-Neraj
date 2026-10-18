"""Render the repo-native SVG share artwork into a PNG for social crawlers."""
from pathlib import Path
import sys, tempfile
sys.dont_write_bytecode = True
from chrome_cdp import Chrome
root = Path(__file__).resolve().parents[2]
svg = (root/'public/images/landing/social-card.svg').read_text()
with tempfile.TemporaryDirectory(prefix='metohub-social-') as folder:
    page = Path(folder)/'card.html'
    page.write_text('<!doctype html><html><body style="margin:0;width:1200px;height:630px;overflow:hidden">'+svg+'</body></html>')
    with Chrome() as chrome:
        chrome.size(1200,630)
        chrome.visit(page.as_uri())
        chrome.screenshot(root/'public/images/landing/social-card.png')
print('Created 1200 × 630 share image.')
