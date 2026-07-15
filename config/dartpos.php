<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DartPOS API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the settings for the DartPOS API integration.
    | You should set the API URL and your access token in your .env file.
    |
    */

    'api_url' => env('DARTPOS_API_URL', 'https://api.dartpos.com/v1'),
    
    'api_key' => env('DARTPOS_API_KEY', ''),
    
    'api_secret' => env('DARTPOS_API_SECRET', ''),

    // Timeout for API requests in seconds
    'timeout' => env('DARTPOS_TIMEOUT', 30),

    // Enable/Disable automatic syncing of stock
    'auto_sync_stock' => env('DARTPOS_AUTO_SYNC_STOCK', true),
    
    // Default location/store ID in DartPOS to sync with
    'location_id' => env('DARTPOS_LOCATION_ID', 1),

];
