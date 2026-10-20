from pathlib import Path
import argparse,json,re,subprocess,tempfile,time
import sys
sys.dont_write_bytecode = True
from chrome_cdp import Chrome
parser=argparse.ArgumentParser(description='Test the actual landing page UI with mocked signup and verification responses.')
parser.add_argument('--browser',help='Path to Chrome/Chromium')
parser.add_argument('--screenshots',help='Optional output folder for screenshots')
args=parser.parse_args()
project=Path(__file__).resolve().parents[2]
temporary=tempfile.TemporaryDirectory(prefix='metohub-landing-qa-')
p=Path(temporary.name)/'landing.html'
subprocess.run(['php',str(project/'tests/browser/render_landing_fixture.php'),str(p)],cwd=project,check=True)
s=p.read_text();s=re.sub(r'https?://127\.0\.0\.1:8000/',(project/'public').as_uri()+'/',s);s=s.replace('="/storage/','="'+(project/'public/storage').as_uri()+'/');p.write_text(s)
output=Path(args.screenshots) if args.screenshots else Path(temporary.name)
output.mkdir(parents=True,exist_ok=True)
mock='''window.__calls=[];window.__scenario='success';window.fetch=async(url,options)=>{const payload=JSON.parse(options.body);window.__calls.push({url,payload});if(window.__scenario==='network')throw new Error('mock network');if(String(url).endsWith('/resend'))return{ok:true,status:200,json:async()=>({message:'If this account needs verification, a new link has been sent.'})};if(window.__scenario==='validation')return{ok:false,status:422,json:async()=>({errors:{email:['This email is already registered.']}})};if(window.__scenario==='rate')return{ok:false,status:429,json:async()=>({})};return{ok:true,status:201,json:async()=>({success:true,email:payload.email,email_sent:window.__scenario!=='mail-failed',message:window.__scenario==='mail-failed'?'Your account was created, but the email could not be sent. Please retry using Resend link.':'Check your inbox to verify your email, then sign in.'})};};'''
with Chrome(browser=args.browser) as c:
 c.call('Page.addScriptToEvaluateOnNewDocument',{'source':mock})
 c.visit(p.as_uri())
 assert c.js("document.querySelectorAll('[data-demo]').length")==7
 assert c.js("document.querySelectorAll('input[name=theme]').length")==15
 assert c.js("document.documentElement.dataset.theme")== 'light'
 for width in [320,375,768,1440]:
  c.size(width);time.sleep(.2)
  bounds=c.js('({client:document.documentElement.clientWidth,scroll:document.documentElement.scrollWidth})')
  assert bounds['scroll']<=bounds['client']+1,(width,bounds)
  c.screenshot(output/f'landing-{width}.png')
  print(width,'page fits',flush=True)
 c.size(1440)
 for section in ['store-tools','faq']:
  c.js(f"window.__stickyGrid=document.querySelector('#{section} .section-with-sticky-heading');window.__stickyTop=__stickyGrid.getBoundingClientRect().top+scrollY;scrollTo({{top:__stickyTop-16,behavior:'instant'}})")
  time.sleep(.1)
  pinned=c.js("__stickyGrid.querySelector('.section-sticky-heading').getBoundingClientRect().top")
  assert abs(pinned-96)<2,(section,'heading should stay below the header',pinned)
  c.js("scrollTo({top:__stickyTop+__stickyGrid.offsetHeight-40,behavior:'instant'})")
  time.sleep(.1)
  bounds=c.js("({heading:__stickyGrid.querySelector('.section-sticky-heading').getBoundingClientRect().bottom,parent:__stickyGrid.getBoundingClientRect().bottom,top:__stickyGrid.querySelector('.section-sticky-heading').getBoundingClientRect().top})")
  assert bounds['heading']<=bounds['parent']+1 and bounds['top']<96,(section,'heading must stop at the end',bounds)
 c.size(375)
 assert c.js("getComputedStyle(document.querySelector('#faq .section-sticky-heading')).position")=='static'
 c.size(1440)
 assert c.js("document.querySelectorAll('[data-review]').length")==6
 assert c.js("document.querySelectorAll('[data-review-clone]').length")==6
 c.js("document.getElementById('reviewViewport').scrollIntoView({block:'center',behavior:'instant'});document.activeElement.blur()")
 c.call('Input.dispatchMouseEvent',{'type':'mouseMoved','x':0,'y':0})
 time.sleep(.25)
 review_start=c.js("document.getElementById('reviewViewport').scrollLeft")
 time.sleep(.4)
 assert c.js("document.getElementById('reviewViewport').scrollLeft")>review_start,'reviews should move right to left'
 c.js("document.getElementById('reviewMotion').click()")
 review_stop=c.js("document.getElementById('reviewViewport').scrollLeft")
 time.sleep(.2)
 assert c.js("document.getElementById('reviewViewport').scrollLeft")==review_stop
 c.js("document.getElementById('reviewNext').click()")
 time.sleep(.6)
 assert c.js("document.getElementById('reviewViewport').scrollLeft")>review_stop
 print('PASS: six seller stories, right-to-left review scrolling, pause/arrows and bounded desktop sticky headings',flush=True)
 c.size(1440)
 c.js("document.getElementById('demoViewport').scrollIntoView({block:'center'});document.activeElement.blur()")
 c.call('Input.dispatchMouseEvent',{'type':'mouseMoved','x':0,'y':0})
 time.sleep(.25)
 start=c.js("document.getElementById('demoViewport').scrollLeft")
 time.sleep(.4)
 assert c.js("document.getElementById('demoViewport').scrollLeft")>start,'carousel should move right to left'
 c.js("document.getElementById('demoMotion').click()")
 assert c.js("document.getElementById('demoMotion').getAttribute('aria-pressed')")=='true'
 stopped=c.js("document.getElementById('demoViewport').scrollLeft")
 time.sleep(.2)
 assert c.js("document.getElementById('demoViewport').scrollLeft")==stopped
 c.js("document.getElementById('demoNext').click()")
 time.sleep(.6)
 assert c.js("document.getElementById('demoViewport').scrollLeft")>stopped
 assert c.js("[...document.querySelectorAll('[data-carousel-clone]')].filter(e=>e.getAttribute('aria-hidden')==='true'&&e.tabIndex===-1).length")==7
 c.js("const v=document.getElementById('demoViewport'),t=document.getElementById('demoTrack');v.scrollLeft=t.querySelector('[data-carousel-clone]').offsetLeft-t.querySelector('[data-demo]').offsetLeft-2;document.getElementById('demoMotion').click()")
 time.sleep(.3)
 assert c.js("document.getElementById('demoViewport').scrollLeft")<30,'carousel should wrap seamlessly'
 c.call('Emulation.setEmulatedMedia',{'features':[{'name':'prefers-reduced-motion','value':'reduce'}]})
 c.visit(p.as_uri())
 c.js("document.getElementById('demoViewport').scrollIntoView({block:'center'})")
 assert c.js("document.getElementById('demoMotion').hidden"),'reduced motion should keep the carousel manual'
 assert c.js("document.getElementById('reviewMotion').hidden"),'reduced motion should keep seller stories manual'
 stopped=c.js("document.getElementById('demoViewport').scrollLeft")
 time.sleep(.2)
 assert c.js("document.getElementById('demoViewport').scrollLeft")==stopped
 c.js("document.getElementById('demoNext').click()")
 assert c.js("document.getElementById('demoViewport').scrollLeft")>stopped
 c.call('Emulation.setEmulatedMedia',{'features':[]})
 for width in [1440,375]:
  c.size(width)
  c.js("window.scrollTo(0,document.getElementById('demos').getBoundingClientRect().top+window.scrollY-70)")
  time.sleep(.1)
  c.screenshot(output/f'landing-carousel-{width}.png')
 if args.screenshots:
  for width in [375,1440]:
   c.size(width)
   for section in ['client-reviews','pricing']:
    c.js(f"document.getElementById('{section}')?.scrollIntoView({{block:'start',behavior:'instant'}})")
    time.sleep(.15)
    c.screenshot(output/f'landing-{section}-{width}.png')
 print('PASS: carousel direction, pause, navigation, seamless loop and reduced motion',flush=True)
 for plan in ['base','pro','enterprise']:
  c.js(f"document.querySelector('[data-start][data-plan={plan}]').click()")
  assert c.js('document.getElementById("wizard").open')
  assert c.js('document.getElementById("planSel").value')==plan
  c.js('document.getElementById("wClose").click()')
 c.size(375)
 def step_to_submit():
  c.js("document.querySelector('[data-start][data-plan=pro]').click();document.getElementById('wNext').click()")
  assert c.js("document.getElementById('err_email').textContent.length>0")
  c.js("document.getElementById('f_name').value='QA Merchant';document.getElementById('f_email').value='qa@example.test';document.getElementById('f_phone').value='+91 9000000000';document.getElementById('wNext').click()")
  assert c.js("document.querySelector('.pane.active').dataset.step")=='2'
  c.js("document.getElementById('f_biz').value='QA Brand';document.getElementById('f_country').value='India';document.getElementById('wNext').click()")
  assert c.js("document.querySelector('.pane.active').dataset.step")=='3'
  assert c.js("document.getElementById('wizard').scrollWidth<=document.getElementById('wizard').clientWidth")
  time.sleep(.4)
  c.screenshot(output/'landing-signup-mobile.png')
  assert c.js("document.getElementById('wBar').style.width")== '100%'
  c.js("document.getElementById('f_pw').value='qa-password-123';document.getElementById('f_pw2').value='qa-password-123'")
 def wait_idle():
  for _ in range(60):
   if not c.js('document.getElementById("wNext").disabled'):return
   time.sleep(.03)
  raise RuntimeError('submission did not settle')
 step_to_submit()
 c.js("window.__scenario='validation';document.getElementById('wNext').click()")
 wait_idle();assert c.js("document.querySelector('.pane.active').dataset.step")=='1'
 assert 'already registered' in c.js("document.getElementById('err_email').textContent")
 c.js("document.getElementById('f_email').value='qa2@example.test';document.getElementById('wNext').click();document.getElementById('wNext').click();window.__scenario='network';document.getElementById('wNext').click()")
 wait_idle();assert c.js("document.getElementById('wDone').hidden")
 assert 'could not confirm' in c.js("document.getElementById('formStatus').textContent")
 c.js("window.__scenario='rate';document.getElementById('wNext').click()")
 wait_idle();assert 'wait a minute' in c.js("document.getElementById('formStatus').textContent")
 c.js("window.__scenario='mail-failed';document.getElementById('wNext').click()")
 wait_idle();assert not c.js("document.getElementById('wDone').hidden")
 assert c.js("document.getElementById('doneTitle').textContent")=='Your account is created'
 assert 'could not be sent' in c.js("document.getElementById('doneMessage').textContent")
 assert c.js("document.getElementById('f_pw').value") == ''
 c.js("document.getElementById('resendVerification').click()")
 time.sleep(.1);assert c.js("document.getElementById('resendVerification').disabled")
 assert 'new link has been sent' in c.js("document.getElementById('resendStatus').textContent")
 payload=c.js("window.__calls.find(c=>c.url.endsWith('/saas/register')).payload")
 assert payload['business']=='QA Brand' and payload['country']=='India' and payload['whatsapp']=='+91 9000000000' and payload['theme']=='template_7' and payload['plan']=='pro'
 c.js("document.getElementById('wClose').click()")
 c.visit(p.as_uri());step_to_submit();c.js("document.getElementById('wNext').click()")
 wait_idle();assert c.js("document.getElementById('doneTitle').textContent")=='Check your email'
 assert c.js("document.getElementById('resendVerification').disabled")
 c.js("document.getElementById('wClose').click()")
 c.size(375);c.js("document.getElementById('menuBtn').click()")
 assert not c.js("document.getElementById('mm').hidden")
 # The fixture is a local file; prevent its absolute homepage link from leaving
 # the fixture while still exercising the real menu's click handler.
 c.js("const link=document.querySelector('#mm a');link.addEventListener('click',e=>e.preventDefault(),{once:true});link.click()")
 assert c.js("document.getElementById('mm').hidden")
 print('PASS: seven demos, 15 themes, plan selection, required fields, server validation, network/429 handling, real-result success/failure states, resend, mobile menu. All responses mocked; no account or email created.')

temporary.cleanup()
