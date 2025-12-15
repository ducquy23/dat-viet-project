<?php

return [
    'client_id' => env('PAYOS_CLIENT_ID'),
    'api_key' => env('PAYOS_API_KEY'),
    'checksum_key' => env('PAYOS_CHECKSUM_KEY'),
    'base_url' => env('PAYOS_BASE_URL', 'https://api-merchant.payos.vn'),
    'return_url' => env('PAYOS_RETURN_URL', env('APP_URL') . '/payment/success'),
    'cancel_url' => env('PAYOS_CANCEL_URL', env('APP_URL') . '/payment/cancel'),
    'expire_minutes' => (int) env('PAYOS_EXPIRE_MINUTES', 10),
];

