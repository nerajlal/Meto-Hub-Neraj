(() => {
  'use strict';
  const element = document.getElementById('billing-config');
  if (!element) return;
  const config = JSON.parse(element.textContent);
  const feedback = document.getElementById('billing-feedback');
  const buttons = [...document.querySelectorAll('.subscribe-btn,.resume-checkout')];
  const disabled = new Map(buttons.map(button => [button, button.disabled]));
  let processing = false;
  let verifying = false;
  const show = (message, error = false) => {
    feedback.textContent = message;
    feedback.className = `alert alert-${error ? 'warning' : 'info'}`;
    feedback.hidden = false;
  };
  const unlock = () => { processing = false; buttons.forEach(button => button.disabled = disabled.get(button)); };
  async function request(url, data) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 30000);
    try {
      const response = await fetch(url, {method: data ? 'POST' : 'GET', credentials: 'same-origin', signal: controller.signal,
        headers: {Accept: 'application/json', ...(data ? {'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf} : {})},
        ...(data ? {body: JSON.stringify(data)} : {})});
      const payload = await response.json().catch(() => ({}));
      if (!response.ok || payload.success === false) {
        const message = response.status === 419 ? 'Your session expired. Reload Billing and refresh provider status before retrying.'
          : payload.message || Object.values(payload.errors || {}).flat()[0] || 'Billing could not confirm the request. Refresh provider status before retrying.';
        throw new Error(message);
      }
      return payload;
    } finally { clearTimeout(timer); }
  }
  async function confirm(result) {
    verifying = true;
    show('Checking payment with Razorpay. Please keep this page open…');
    try {
      let state = await request(config.verify, {
        razorpay_payment_id: result.razorpay_payment_id,
        razorpay_subscription_id: result.razorpay_subscription_id,
        razorpay_signature: result.razorpay_signature,
      });
      for (let attempt = 0; !state.paid_access && attempt < 6; attempt++) {
        show('Waiting for provider confirmation. Do not pay again.');
        await new Promise(resolve => setTimeout(resolve, 2000));
        state = await request(config.status);
      }
      show(state.message);
      if (state.paid_access) location.reload();
    } catch (error) {
      show(`${error.name === 'AbortError' ? 'Confirmation timed out.' : error.message} Do not pay again; refresh provider status to check the result.`, true);
    } finally { verifying = false; unlock(); }
  }
  buttons.forEach(button => button.addEventListener('click', async () => {
    if (processing || button.disabled || !config.configured) return;
    if (typeof window.Razorpay !== 'function') {
      show('Razorpay checkout did not load. Check your connection and reload this page.', true);
      return;
    }
    processing = true;
    buttons.forEach(item => item.disabled = true);
    show('Preparing your subscription checkout…');
    try {
      const data = await request(config.subscribe, {plan_tier: button.dataset.tier,
        cycle: button.dataset.cycle || document.getElementById('billing-cycle').value});
      if (!data.key || !data.subscription_id) throw new Error('Checkout details are incomplete. Refresh provider status before retrying.');
      const checkout = new window.Razorpay({key: data.key, subscription_id: data.subscription_id, name: 'MetoHub',
        description: `${data.plan_name || 'Store'} · ${data.cycle || 'subscription'} subscription`, prefill: config.prefill,
        theme: {color: '#7c3aed'}, handler: confirm,
        modal: {ondismiss: () => {
          if (!verifying) { show('Checkout closed. No paid access has been confirmed. Resume the same plan or refresh provider status before changing plans.'); unlock(); }
        }}});
      checkout.on('payment.failed', () => show('The payment attempt failed. You can retry inside this checkout or close it and refresh provider status.', true));
      checkout.open();
    } catch (error) {
      show(error.name === 'AbortError' ? 'Checkout preparation timed out. Refresh provider status before retrying; do not start a second payment.' : error.message, true);
      unlock();
    }
  }));
})();
