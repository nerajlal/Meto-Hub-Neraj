<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MerchantOrderAlert;
use App\Models\MerchantPushSubscription;
use App\Services\MerchantWebPush;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderNotificationController extends Controller
{
    private function alerts()
    {
        return MerchantOrderAlert::where('tenant_id', request()->attributes->get('resolved_tenant_id'))->where('user_id', auth()->id());
    }

    private function devices()
    {
        return MerchantPushSubscription::where('tenant_id', request()->attributes->get('resolved_tenant_id'))->where('user_id', auth()->id());
    }

    public function index(MerchantWebPush $push)
    {
        $alerts = $this->alerts()->with('order')->latest('id')->paginate(20);
        $devices = $this->devices()->where('enabled', true)->latest()->get();
        $pushConfigured = $push->configured();

        return view('admin.notifications.index', compact('alerts', 'devices', 'pushConfigured'));
    }

    public function feed()
    {
        return response()->json(['unread_count' => $this->alerts()->whereNull('read_at')->count(), 'alerts' => $this->alerts()->with('order')->latest('id')->limit(8)->get()->map(fn ($alert) => [
            'id' => $alert->id, 'unread' => ! $alert->read_at, 'title' => 'New order #'.$alert->order?->order_number, 'time' => $alert->created_at->diffForHumans(),
            'url' => route('admin.notifications.open', ['id' => $alert->id]),
        ])], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function readAll()
    {
        $this->alerts()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function read(int $id)
    {
        $alert = $this->alerts()->findOrFail($id);
        $alert->update(['read_at' => $alert->read_at ?? now()]);

        return response()->json(['success' => true]);
    }

    public function open(int $id)
    {
        $alert = $this->alerts()->findOrFail($id);

        // Read state changes are explicit POST requests; link previews must not consume alerts.
        return redirect()->route('admin.orders.show', ['id' => $alert->order_id, 'notification_alert' => $alert->id]);
    }

    public function subscribe(Request $request, MerchantWebPush $push)
    {
        abort_unless($push->configured(), 503, 'Device notifications are not configured on the server yet.');
        $data = $request->validate(['endpoint' => 'required|string|max:4096', 'keys.p256dh' => 'required|string|max:120', 'keys.auth' => 'required|string|max:50']);
        if (! $push->allowedEndpoint($data['endpoint'])) {
            throw ValidationException::withMessages(['endpoint' => 'Unsupported push provider endpoint. Use a supported browser.']);
        }
        foreach (['p256dh' => 65, 'auth' => 16] as $key => $length) {
            $value = $data['keys'][$key];
            $decoded = base64_decode(strtr($value, '-_', '+/'), true);
            if (! preg_match('/^[A-Za-z0-9_-]+={0,2}$/', $value) || $decoded === false || strlen($decoded) !== $length || ($key === 'p256dh' && ord($decoded[0]) !== 4)) {
                throw ValidationException::withMessages(['keys.'.$key => 'Invalid browser push key.']);
            }
        }
        $hash = hash('sha256', $data['endpoint']);
        $existing = MerchantPushSubscription::where('endpoint_hash', $hash)->first();
        if ((! $existing || (int) $existing->user_id !== auth()->id() || ! $existing->enabled) && $this->devices()->where('enabled', true)->count() >= 10) {
            throw ValidationException::withMessages(['endpoint' => 'You have 10 connected devices. Remove an old device first.']);
        }
        $device = MerchantPushSubscription::updateOrCreate(['endpoint_hash' => $hash], [
            'tenant_id' => $request->attributes->get('resolved_tenant_id'), 'user_id' => auth()->id(), 'endpoint' => $data['endpoint'], 'public_key' => $data['keys']['p256dh'], 'auth_token' => $data['keys']['auth'], 'origin' => $request->getSchemeAndHttpHost(), 'enabled' => true,
        ]);

        return response()->json(['success' => true, 'device_id' => $device->id]);
    }

    public function deviceStatus(Request $request)
    {
        $request->validate(['endpoint' => 'required|string|max:4096']);

        return response()->json(['enabled' => $this->devices()->where('endpoint_hash', hash('sha256', $request->endpoint))->where('enabled', true)->exists()], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string|max:4096']);
        $this->devices()->where('endpoint_hash', hash('sha256', $request->endpoint))->update(['enabled' => false]);

        return response()->json(['success' => true]);
    }

    public function removeDevice(int $id)
    {
        $this->devices()->findOrFail($id)->update(['enabled' => false]);

        return back()->with('success','Device notifications disabled.');
    }
}
