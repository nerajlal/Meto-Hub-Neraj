<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DartPosService
{
    protected $apiUrl;
    protected $apiKey;
    protected $apiSecret;
    protected $timeout;

    public function __construct()
    {
        $this->apiUrl = config('dartpos.api_url');
        $this->apiKey = config('dartpos.api_key');
        $this->apiSecret = config('dartpos.api_secret');
        $this->timeout = config('dartpos.timeout', 30);
    }

    /**
     * Get configured HTTP client for DartPOS
     */
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-Api-Secret' => $this->apiSecret,
            'Accept' => 'application/json',
        ])->timeout($this->timeout);
    }

    /**
     * Fetch all products from DartPOS
     */
    public function getProducts($page = 1, $limit = 100)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/products", [
                'page' => $page,
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('DartPOS getProducts error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('DartPOS getProducts exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch single product by SKU
     */
    public function getProductBySku($sku)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/products/sku/{$sku}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('DartPOS getProductBySku exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Push a new order to DartPOS
     */
    public function pushOrder(array $orderData)
    {
        try {
            $response = $this->client()->post("{$this->apiUrl}/orders", $orderData);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('DartPOS pushOrder error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('DartPOS pushOrder exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update stock in DartPOS (if needed)
     */
    public function updateStock($sku, $quantity, $locationId = null)
    {
        $locationId = $locationId ?? config('dartpos.location_id');
        
        try {
            $response = $this->client()->put("{$this->apiUrl}/inventory/{$sku}", [
                'quantity' => $quantity,
                'location_id' => $locationId
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('DartPOS updateStock exception: ' . $e->getMessage());
            return false;
        }
    }
}
