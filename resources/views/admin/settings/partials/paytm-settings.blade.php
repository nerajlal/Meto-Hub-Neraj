<div class="col-12">
<details class="gateway-settings card border-0 rounded-4 shadow-sm bg-white" data-gateway-settings="paytm" @if(collect(array_keys($errors->getMessages()))->contains(fn ($field) => str_starts_with($field, 'paytm_'))) open @endif>
    @include('admin.settings.partials.gateway-summary', ['gateway' => 'paytm', 'gatewayName' => 'Paytm'])
    <div class="gateway-settings-body p-3 p-md-4 border-top">
    <label class="form-check form-switch d-flex align-items-center gap-2 mb-4">
        <input type="checkbox" class="form-check-input" name="paytm_enabled" id="paytm_enabled" value="1" @checked(old('paytm_enabled', $paymentSetting->paytm_enabled))>
        <span class="small fw-semibold">Enable Paytm payments</span>
    </label>
    @include('admin.settings.partials.gateway-onboarding', ['gateway' => 'paytm'])
    <label for="paytm_environment" class="form-label small fw-semibold">Environment</label>
    <select id="paytm_environment" name="paytm_environment" class="form-select mb-3"><option value="test" @selected(old('paytm_environment', $paymentSetting->paytm_environment) === 'test')>Test / staging</option><option value="live" @selected(old('paytm_environment', $paymentSetting->paytm_environment) === 'live')>Live</option></select>
    <label for="paytm_mid" class="form-label small fw-semibold">Online payment Merchant ID (MID)</label>
    <input id="paytm_mid" name="paytm_mid" type="text" class="form-control mb-3" value="{{ old('paytm_mid', $paymentSetting->paytm_mid) }}" autocomplete="off">
    <label for="paytm_merchant_key" class="form-label small fw-semibold">Merchant Key</label>
    <input id="paytm_merchant_key" name="paytm_merchant_key" type="password" class="form-control" value="" autocomplete="new-password" placeholder="16-character merchant key">
    <p class="small text-muted mt-1">Leave blank to keep the saved key.</p>
    <label for="paytm_website_name" class="form-label small fw-semibold">Website name from Paytm</label>
    <input id="paytm_website_name" name="paytm_website_name" type="text" class="form-control mb-3" value="{{ old('paytm_website_name', $paymentSetting->paytm_website_name) }}" placeholder="WEBSTAGING (test) or DEFAULT (live)">
    <p class="small text-muted">Activate website/app payments in Paytm and use matching environment credentials. The browser callback is supplied automatically for each checkout.</p>
    <p class="small mb-1">Payment-status webhook URL:</p><code class="d-block text-break">{{ rtrim(config('app.url'), '/').route('payment.paytm.webhook', ['tenant' => $paymentSetting->tenant_id], false) }}</code>
    <p class="small text-muted mt-2 mb-0">Configure payment notifications in Paytm. A callback or browser message never marks an order paid without a server status check.</p>
    </div>
</details>
</div>
