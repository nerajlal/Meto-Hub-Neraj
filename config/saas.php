<?php

return [
    'trial_days' => 7,
    'currency' => 'INR',
    'annual_months' => 11,
    'plans' => [
        'base' => [
            'name' => 'Base', 'monthly_price' => 99, 'entitlements' => [],
            'description' => 'Everything you need to get your store up and running quickly.',
            'features' => ['Online Store', 'Unlimited visitors', 'Unlimited orders', 'Sell Unlimited products', 'Free MetoHub Subdomain', 'Chat and call support'],
        ],
        'pro' => [
            'name' => 'Pro', 'monthly_price' => 249, 'entitlements' => ['custom_domain', 'advanced_analytics', 'remove_branding'],
            'description' => 'For growing businesses that need advanced features and scale.',
            'features' => ['Everything in Base, plus:', 'Custom Domain Support', 'Advanced Analytics & Reports', 'No watermark in footer'],
        ],
        'enterprise' => [
            'name' => 'Enterprise', 'monthly_price' => 749, 'entitlements' => ['custom_domain', 'advanced_analytics', 'remove_branding'],
            'description' => 'Maximum performance and dedicated support for large scale operations.',
            'features' => ['Everything in Pro, plus:', 'Priority support coordination', 'Dedicated Account Manager'],
        ],
    ],
];
