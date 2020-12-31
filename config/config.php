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
    | Payment Provider
    |--------------------------------------------------------------------------
    |
    | The default payment provider to use for billing.
    |
    | Note: This may be changed at run time using the
    | $checkout->setPaymentProvider(...) method prior to $checkout->charge().
    |
    */
    'provider' => 'stripe',

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
