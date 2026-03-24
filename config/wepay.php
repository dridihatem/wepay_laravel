<?php

return [
    /*
    | URL prefix for package routes (webhook, invoice create, status).
    */
    'route_prefix' => env('WEPAY_ROUTE_PREFIX', 'wepay'),

    /*
    | UAT default from WePay / ClicToPay documentation. Replace with production when provided.
    */
    'base_url' => env('WEPAY_BASE_URL', 'https://uatapi.clictopay.com'),

    'merchant_id' => env('WEPAY_MERCHANT_ID', ''),

    /*
    | Used to sign invoice creation (HMAC-SHA256 over base64 JSON). Server-side only.
    */
    'secret_key' => env('WEPAY_SECRET_KEY', ''),

    /*
    | Used for GET invoice status (x-api-key header). Server-side only.
    */
    'api_key' => env('WEPAY_API_KEY', ''),

    /*
    | Optional: if WePay documents a shared secret or signature for webhooks, set and verify in controller.
    */
    'webhook_secret' => env('WEPAY_WEBHOOK_SECRET', ''),
];
