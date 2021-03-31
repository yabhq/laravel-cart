<?php

namespace Yab\ShoppingCart\Tests\Factories;

use Yab\ShoppingCart\Models\Order;

$factory->define(Order::class, function () {
    $address = [
        'street' => '123 Test Street',
        'city' => 'Toronto',
        'state_province' => 'ON',
        'zip_postal' => 'L3L 3L3',
        'country' => 'CA',
    ];
    return [
        'custom_fields' => [
            'customer_info' => [
                'name' => 'John Snow',
                'email' => 'johnsnow@example.net',
            ],
            'shipping_address' => $address,
            'billing_address' => $address,
        ],
    ];
});
