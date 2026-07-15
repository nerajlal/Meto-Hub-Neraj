<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DartPosWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from DartPOS
     */
    public function handleInventoryUpdate(Request $request)
    {
        // 1. Validate Webhook Signature
        $signature = $request->header('X-DartPOS-Signature');
        // TODO: Implement signature verification logic here

        // 2. Process payload
        $payload = $request->all();
        
        Log::info('DartPOS Inventory Webhook Received', ['payload' => $payload]);

        if (isset($payload['event']) && $payload['event'] === 'inventory.updated') {
            $sku = $payload['data']['sku'] ?? null;
            $newQuantity = $payload['data']['quantity'] ?? null;

            if ($sku && $newQuantity !== null) {
                // Find variant by SKU and update stock
                $variant = \App\Models\ProductVariant::where('sku', $sku)->first();
                
                if ($variant) {
                    $variant->stock = $newQuantity;
                    $variant->save();
                    
                    return response()->json(['status' => 'success', 'message' => 'Stock updated']);
                }
            }
        }

        return response()->json(['status' => 'ignored'], 200);
    }
}
