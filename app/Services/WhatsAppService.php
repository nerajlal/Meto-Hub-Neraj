<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $phoneNumberId;
    protected $accessToken;
    protected $ownerNumber;

    public function __construct()
    {
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->accessToken = config('services.whatsapp.access_token');
        $this->ownerNumber = config('services.whatsapp.owner_number');
    }

    /**
     * Send order notification to the owner
     */
    public function sendOrderNotification($order)
    {
        if (!$this->phoneNumberId || !$this->accessToken || !$this->ownerNumber) {
            Log::warning('WhatsApp credentials missing. Skipping notification.');
            return false;
        }

        $itemsText = "";
        foreach ($order->items as $item) {
            $itemsText .= "- {$item->name} " . ($item->size ? "({$item->size})" : "") . " x {$item->quantity}\n";
        }

        $address = $order->shipping_address;
        $addressText = "{$address['address']}, {$address['city']}, {$address['state']} - {$address['zip']}";

        $message = "🛍️ *New Order Received!*\n";
        $message .= "-------------------------\n";
        $message .= "🆔 *Order:* #{$order->order_number}\n";
        $message .= "👤 *Customer:* {$order->customer_name}\n";
        $message .= "📞 *Phone:* {$order->customer_phone}\n";
        $message .= "💰 *Total:* ₹" . number_format($order->total_amount, 2) . "\n";
        $message .= "🚚 *Method:* " . strtoupper($order->payment_method) . "\n\n";
        $message .= "📦 *Items:*\n{$itemsText}\n";
        $message .= "📍 *Address:* {$addressText}\n";
        $message .= "-------------------------\n";
        $message .= "✅ View details in Admin Panel.";

        return $this->sendMessage($this->ownerNumber, $message);
    }

    /**
     * Core method to send message via Meta Graph API
     */
    public function sendMessage($to, $message)
    {
        $url = "https://graph.facebook.com/v17.0/{$this->phoneNumberId}/messages";

        try {
            $response = Http::withToken($this->accessToken)->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent to {$to}");
                return true;
            } else {
                Log::error("WhatsApp API Error: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp Connection Error: " . $e->getMessage());
            return false;
        }
    }
}
