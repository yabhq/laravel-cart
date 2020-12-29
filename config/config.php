<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Global Settings
    |--------------------------------------------------------------------------
    |
    | The default currency code to use for processing payments.
    |
    */
    'currency' => 'USD',

    /*
    |--------------------------------------------------------------------------
    | Stripe Payment Provider
    |--------------------------------------------------------------------------
    |
    | The public and private Stripe API keys used to connect to the Stripe
    | account for billing purposes.
    |
    */
    'stripe' => [
        'secret_key' => env('STRIPE_SECRET'),
        'public_key' => env('STRIPE_KEY'),
    ],
];
