<?php

return [
    'queue_connection' => env('ORDER_NOTIFICATIONS_QUEUE_CONNECTION', 'database'),
    'queue' => 'order-notifications',
    'vapid' => ['subject' => env('WEBPUSH_VAPID_SUBJECT', 'mailto:info@metora.in'), 'public_key' => env('WEBPUSH_VAPID_PUBLIC_KEY'), 'private_key' => env('WEBPUSH_VAPID_PRIVATE_KEY')],
    'endpoint_hosts' => ['fcm.googleapis.com', 'updates.push.services.mozilla.com', 'web.push.apple.com'],
];
