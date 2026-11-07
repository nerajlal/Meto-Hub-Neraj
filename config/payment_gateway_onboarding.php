<?php

return [
    'razorpay' => [
        'name' => 'Razorpay',
        'signup' => 'https://accounts.razorpay.com/auth/',
        'dashboard' => 'https://dashboard.razorpay.com/',
        'guidance' => 'Apply for a payment gateway account using your store website and business details. After activation, get your Key ID and Key Secret from the dashboard.',
    ],
    'paytm' => [
        'name' => 'Paytm',
        'signup' => 'https://dashboard.paytmpayments.com/',
        'dashboard' => 'https://dashboard.paytmpayments.com/',
        'guidance' => 'Create an account or sign in with your existing Paytm account, then activate website/app payments. Get the online-payment MID, Merchant Key and Website Name after approval.',
    ],
    'phonepe' => [
        'name' => 'PhonePe',
        'signup' => 'https://business.phonepe.com/register',
        'dashboard' => 'https://business.phonepe.com/login',
        'guidance' => 'Choose Website/App/Social Media during registration and apply for Payment Gateway access. After activation, get your Standard Checkout V2 Client ID, Client Secret and Client Version.',
    ],
];
