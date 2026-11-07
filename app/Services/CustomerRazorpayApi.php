<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CustomerRazorpayApi
{
    public function call(array $credentials, string $method, string $path, array $data = []): array
    {
        // Never retry order creation: a timeout can occur after the provider creates the order.
        $http = Http::withBasicAuth($credentials['key'], $credentials['secret'])
            ->acceptJson()->connectTimeout(5)->timeout(20);
        $response = $method === 'POST'
            ? $http->post('https://api.razorpay.com/v1/'.$path, $data)
            : $http->get('https://api.razorpay.com/v1/'.$path, $data);
        $response->throw();
        return $response->json();
    }
}
