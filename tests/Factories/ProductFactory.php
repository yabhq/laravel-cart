<?php

namespace Yab\ShoppingCart\Tests\Factories;

use Yab\ShoppingCart\Tests\Models\Product;

$factory->define(Product::class, function () {
    return [
        'price' => 19.95,
    ];
});
