<div class="col-12">
<details class="gateway-settings card border-0 rounded-4 shadow-sm bg-white" data-gateway-settings="phonepe" @if(collect(array_keys($errors->getMessages()))->contains(fn ($field) => str_starts_with($field, 'phonepe_'))) open @endif>
    @include('admin.settings.partials.gateway-summary', ['gateway' => 'phonepe', 'gatewayName' => 'PhonePe'])
    <div class="gateway-settings-body p-3 p-md-4 border-top">
    <label class="form-check form-switch d-flex align-items-center gap-2 mb-4">
        <input type="checkbox" class="form-check-input" name="phonepe_enabled" id="phonepe_enabled" value="1" @checked(old('phonepe_enabled', $paymentSetting->phonepe_enabled))>
        <span class="small fw-semibold">Enable PhonePe payments</span>
    </label>
    @include('admin.settings.partials.gateway-onboarding', ['gateway' => 'phonepe'])
    <label for="phonepe_environment" class="form-label small fw-semibold">Environment</label>
    <select id="phonepe_environment" name="phonepe_environment" class="form-select mb-3"><option value="test" @selected(old('phonepe_environment', $paymentSetting->phonepe_environment) === 'test')>Test / sandbox</option><option value="live" @selected(old('phonepe_environment', $paymentSetting->phonepe_environment) === 'live')>Live</option></select>
    <label for="phonepe_client_id" class="form-label small fw-semibold">Client ID</label>
    <input id="phonepe_client_id" name="phonepe_client_id" class="form-control mb-3" value="{{ old('phonepe_client_id', $paymentSetting->phonepe_client_id) }}" autocomplete="off">
    <label for="phonepe_client_version" class="form-label small fw-semibold">Client Version</label>
    <input id="phonepe_client_version" name="phonepe_client_version" type="number" min="1" class="form-control mb-3" value="{{ old('phonepe_client_version', $paymentSetting->phonepe_client_version) }}">
    <label for="phonepe_client_secret" class="form-label small fw-semibold">Client Secret</label>
    <input id="phonepe_client_secret" name="phonepe_client_secret" type="password" class="form-control" value="" autocomplete="new-password"><p class="small text-muted mt-1">Leave blank to keep the saved secret.</p>
    <label for="phonepe_webhook_username" class="form-label small fw-semibold">Webhook username</label>
    <input id="phonepe_webhook_username" name="phonepe_webhook_username" class="form-control mb-3" value="{{ old('phonepe_webhook_username', $paymentSetting->phonepe_webhook_username) }}" autocomplete="off">
    <label for="phonepe_webhook_password" class="form-label small fw-semibold">Webhook password</label>
    <input id="phonepe_webhook_password" name="phonepe_webhook_password" type="password" class="form-control" value="" autocomplete="new-password"><p class="small text-muted mt-1">Use at least 16 characters. Leave blank to keep the saved password.</p>
    <p class="small text-muted">Uses Standard Checkout V2. Legacy Merchant ID / Salt Key credentials cannot be used here.</p>
    <p class="small mb-1">Webhook URL:</p><code class="d-block text-break">{{ rtrim(config('app.url'), '/').route('payment.phonepe.webhook', ['tenant' => $paymentSetting->tenant_id], false) }}</code>
    <p class="small text-muted mt-2 mb-0">Set the same username/password in PhonePe and enable checkout.order.completed and checkout.order.failed events.</p>
    </div>
</details>
</div>
