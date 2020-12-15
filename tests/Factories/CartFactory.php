<?php

namespace Yab\ShoppingCart\Tests\Factories;

use Yab\ShoppingCart\Models\Cart;

$factory->define(Cart::class, function () {
    return [
        'shipping_address' => [
            'street' => '123 Test Street',
            'city' => 'Toronto',
            'state_province' => 'ON',
            'zip_postal' => 'L3L 3L3',
            'country' => 'CA',
        ],
    ];
});
