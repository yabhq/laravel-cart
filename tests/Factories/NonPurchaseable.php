<?php

namespace Yab\ShoppingCart\Tests\Factories;

use Yab\ShoppingCart\Tests\Models\NonPurchaseable;

$factory->define(NonPurchaseable::class, function () {
    return [
        'price' => 19.95,
    ];
});
