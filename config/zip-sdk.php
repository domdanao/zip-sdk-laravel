<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zip API Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for the Zip payment API.
    |
    */

    'api_server' => env('ZIP_API_SERVER', 'https://api.zip.ph'),
    'public_key' => env('ZIP_PUBLIC_KEY'),
    'secret_key' => env('ZIP_SECRET_KEY'),
    'version' => env('ZIP_API_VERSION', 'v2'),

    // Default settings
    'defaults' => [
        'currency' => 'PHP',
        'payment_methods' => ['card', 'gcash', 'maya'],
        'locale' => 'en',
    ],
];
