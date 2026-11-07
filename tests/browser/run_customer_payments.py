from pathlib import Path
import re, subprocess, sys, tempfile, time
sys.dont_write_bytecode = True
from chrome_cdp import Chrome
project = Path(__file__).resolve().parents[2]
with tempfile.TemporaryDirectory(prefix='customer-payment-qa-') as folder:
    with Chrome() as c:
        for mode in ['payment', 'customer-checkout', 'paytm-checkout', 'phonepe-checkout']:
            page = Path(folder) / (mode + '.html')
            subprocess.run(['php', str(project/'tests/browser/render_settings_fixture.php'), str(page), '--'+mode], cwd=project, check=True)
            html = re.sub(r'https?://127\.0\.0\.1:8000/', (project/'public').as_uri()+'/', page.read_text())
            html = re.sub(r'<script src="https://checkout\.razorpay\.com/v1/checkout\.js"></script>', '', html)
            page.write_text(html)
            c.visit(page.as_uri())
            for width in [375, 1440]:
                c.size(width); time.sleep(.15)
                assert not c.js('document.documentElement.scrollWidth > innerWidth + 1'), (mode,width)
            if mode == 'payment':
                assert c.js("document.getElementById('razorpay_secret').value") == ''
                assert c.js("document.getElementById('razorpay_webhook_secret').value") == ''
                assert c.js("!document.getElementById('paytm_enabled').disabled && !document.getElementById('phonepe_enabled').disabled")
                assert c.js("document.getElementById('paytm_merchant_key').value === '' && document.getElementById('phonepe_client_secret').value === ''")
                assert not c.js("document.getElementById('razorpay_enabled').disabled")
            elif mode == 'customer-checkout':
                assert c.js("!!window.jQuery && !!document.getElementById('customer-payment-status')")
                c.js("""(() => {
                    window.paymentRequests = [];
                    window.Razorpay = function(options) {window.checkoutOptions=options;this.on=()=>{};this.open=()=>{};};
                    $.ajax = function(options) {
                        window.paymentRequests.push({url:options.url,data:options.data});
                        if(options.url.includes('/payment/initiate')) options.success({success:true,key:'rzp_test_fixture',checkout_id:'checkout-fixture',order_id:'order_fixture',amount:10000,currency:'INR',store_name:'Fixture'});
                        else options.success({success:false,status:'review',message:'Payment received; do not pay again.'});
                        options.complete?.();
                    };
                    $('input[name=payment_method]').prop('checked',false);
                    $('input[name=payment_method][value=razorpay]').prop('checked',true);
                    $('#main-checkout-form').triggerHandler('submit');
                })()""")
                assert c.js("paymentRequests.length") == 1
                assert not c.js("new URLSearchParams(paymentRequests[0].data).has('amount')")
                assert c.js("new URLSearchParams(paymentRequests[0].data).has('checkout_request_id')")
                c.js("checkoutOptions.handler({razorpay_payment_id:'pay_fixture',razorpay_order_id:'order_fixture',razorpay_signature:'signature-fixture'})")
                assert c.js("paymentRequests[1].url.includes('/payment/verify')")
                assert c.js("paymentRequests[1].data.razorpay_signature") == 'signature-fixture'
                assert not c.js("paymentRequests.some(r=>r.url.includes('/order/place'))")
                assert 'do not pay again' in c.js("document.getElementById('customer-payment-message').textContent")
                c.js("document.getElementById('customer-payment-status').click()")
                assert c.js("paymentRequests[2].url.includes('/payment/status')")
            else:
                gateway = 'paytm' if mode == 'paytm-checkout' else 'phonepe'
                c.js("sessionStorage.clear()")
                c.js("window.fixtureGateway = " + repr(gateway))
                c.js("""(() => {
                    window.paymentRequests=[];
                    const append = document.head.appendChild.bind(document.head);
                    document.head.appendChild = function(node) {
                        if(node.tagName === 'SCRIPT' && node.src.includes('merchantpgpui/checkoutjs')) {
                            window.Paytm={CheckoutJS:{onLoad:function(cb){cb()},init:function(config){window.paytmConfig=config;return Promise.resolve()},invoke:function(){window.paytmInvoked=true},close:function(){}}};
                            queueMicrotask(function(){node.onload()}); return node;
                        }
                        return append(node);
                    };
                    $.ajax = function(options) {
                        window.paymentRequests.push({url:options.url,data:options.data});
                        if(options.url.includes('/payment/initiate')) options.success({success:true,gateway:window.fixtureGateway,checkout_id:'checkout-fixture',order_id:'ORDERfixture',amount:10000,currency:'INR',mid:'MIDfixture',txn_token:'token-fixture',script_url:'https://securestage.paytmpayments.com/merchantpgpui/checkoutjs/merchants/MIDfixture.js',payment_url:'#phonepe-fixture'});
                        else options.success({success:false,status:'review',message:'Payment received; do not pay again.'});
                        options.complete?.();
                    };
                    $('input[name=payment_method]').prop('checked',false);
                    $('input[name=payment_method][value='+window.fixtureGateway+']').prop('checked',true);
                    $('#main-checkout-form').triggerHandler('submit');
                })()""")
                time.sleep(.1)
                assert c.js("paymentRequests.length") == 1
                assert not c.js("new URLSearchParams(paymentRequests[0].data).has('amount')")
                assert c.js("new URLSearchParams(paymentRequests[0].data).get('gateway')") == gateway
                if gateway == 'paytm':
                    assert c.js("window.paytmInvoked === true")
                    assert c.js("paytmConfig.data.tokenType") == 'TXN_TOKEN'
                    c.js("paytmConfig.handler.transactionStatus({STATUS:'TXN_SUCCESS',TXNAMOUNT:'1.00'})")
                    assert c.js("paymentRequests[1].url.includes('/payment/status')")
                else:
                    assert c.js("location.hash") == '#phonepe-fixture'
                assert not c.js("paymentRequests.some(r=>r.url.includes('/order/place'))")
            print('PASS:', mode, 'mobile/desktop layout and secure payment controls', flush=True)
