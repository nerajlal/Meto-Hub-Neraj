"""Exercise actual billing JS with mocked Razorpay/fetch; never creates subscriptions."""
from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode = True
from chrome_cdp import Chrome

project = Path(__file__).resolve().parents[2]
mock = '''
window.__calls=[];window.__checkout=null;window.__scenario='normal';window.__opened=0;
window.Razorpay=class{constructor(options){this.options=options;this.handlers={};window.__checkout=this;}on(name,handler){this.handlers[name]=handler;}open(){window.__opened++;}};
window.fetch=async(url,options={})=>{
 const body=options.body?JSON.parse(options.body):null;if(String(url).includes('/billing/'))window.__calls.push({url:String(url),body});
 if(window.__scenario==='network')throw new TypeError('Mock connection failure');
 if(String(url).endsWith('/subscribe')){
   await new Promise(resolve=>setTimeout(resolve,50));
   if(window.__scenario==='conflict')return{ok:false,status:409,json:async()=>({message:'An existing checkout needs reconciliation.'})};
   return{ok:true,status:200,json:async()=>({success:true,key:'rzp_test_fixture',subscription_id:'sub_fixture',plan_name:'Pro',cycle:body.cycle})};
 }
 return{ok:true,status:200,json:async()=>({success:true,paid_access:false,status:'authenticated',message:'Waiting for provider confirmation; do not pay again.'})};
};
'''
with tempfile.TemporaryDirectory(prefix='metohub-billing-qa-') as folder:
    fixtures={}
    for mode in ['billing','billing-resume','expired']:
        file=Path(folder)/(mode+'.html')
        subprocess.run(['php',str(project/'tests/browser/render_settings_fixture.php'),str(file),'--'+mode],cwd=project,check=True)
        html=re.sub(r'<script src="https://checkout\.razorpay\.com/v1/checkout\.js"></script>','',file.read_text())
        file.write_text(re.sub(r'https?://127\.0\.0\.1:8000/',(project/'public').as_uri()+'/',html))
        fixtures[mode]=file.as_uri()
    with Chrome() as c:
        c.call('Page.addScriptToEvaluateOnNewDocument',{'source':mock})
        c.visit(fixtures['billing'])
        for width in [320,375,768,1440]:
            c.size(width);time.sleep(.15)
            assert not c.js('document.documentElement.scrollWidth>innerWidth+1'),width
        c.js("document.getElementById('billing-cycle').value='yearly';const b=document.querySelector('[data-tier=pro]');b.click();b.click()")
        time.sleep(.2)
        assert c.js("__calls.filter(c=>c.url.endsWith('/subscribe')).length")==1, c.js("JSON.stringify({calls:__calls,configured:document.getElementById('billing-config').textContent,feedback:document.getElementById('billing-feedback').textContent})")
        assert c.js("__calls[0].body.cycle")=='yearly'
        assert c.js("__checkout.options.subscription_id")=='sub_fixture'
        assert c.js("__checkout.options.theme.color")=='#7c3aed'
        c.js("__checkout.handlers['payment.failed']()")
        assert 'failed' in c.js("document.getElementById('billing-feedback').textContent")
        assert c.js("document.querySelector('[data-tier=pro]').disabled")
        c.js("__checkout.options.modal.ondismiss()")
        assert not c.js("document.querySelector('[data-tier=pro]').disabled")
        c.js("__scenario='conflict';document.querySelector('[data-tier=base]').click()")
        time.sleep(.2)
        assert 'reconciliation' in c.js("document.getElementById('billing-feedback').textContent")
        c.js("__scenario='network';document.querySelector('[data-tier=base]').click()")
        time.sleep(.1)
        assert not c.js("document.querySelector('[data-tier=base]').disabled")
        c.visit(fixtures['billing'])
        c.js("delete window.Razorpay;document.querySelector('[data-tier=base]').click()")
        assert c.js('__calls.length')==0
        assert 'did not load' in c.js("document.getElementById('billing-feedback').textContent")
        c.visit(fixtures['billing-resume'])
        assert c.js("Array.from(document.querySelectorAll('.subscribe-btn')).every(b=>b.disabled)")
        c.js("document.querySelector('.resume-checkout').click()")
        time.sleep(.2)
        assert c.js('__calls[0].body.plan_tier')=='pro'
        assert c.js('__calls[0].body.cycle')=='yearly'
        c.js("void __checkout.options.handler({razorpay_payment_id:'pay_fixture',razorpay_subscription_id:'sub_fixture',razorpay_signature:'mock-signature'})")
        time.sleep(.1)
        assert c.js("__calls.find(c=>c.url.endsWith('/verify')).body.razorpay_subscription_id")=='sub_fixture'
        assert c.js("document.querySelector('.resume-checkout').disabled")
        assert 'provider confirmation' in c.js("document.getElementById('billing-feedback').textContent")
        c.visit(fixtures['expired'])
        assert c.js("Array.from(document.querySelectorAll('.subscribe-btn')).every(b=>b.disabled)")
        assert 'not configured yet' in c.js('document.body.textContent')
        assert c.js('__calls.length')==0
        print('PASS: responsive Billing, duplicate-click guard, monthly/yearly selection, failure/dismissal, provider/network errors, missing checkout script, existing checkout resumption, pending confirmation and disabled unconfigured checkout. All provider responses mocked.',flush=True)
