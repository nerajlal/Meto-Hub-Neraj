<?php

return [
    // Blank uses APP_URL's hostname, keeping test and production namespaces separate.
    'base' => env('STOREFRONT_DOMAIN'),
    'wildcard_ready' => env('STOREFRONT_WILDCARD_READY', false),
    'reserved' => ['www', 'test', 'admin', 'api', 'app', 'mail', 'smtp', 'ftp', 'status', 'support', 'staging', 'dev', 'assets', 'cdn', 'autodiscover'],
];
