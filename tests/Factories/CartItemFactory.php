<?php

namespace Yab\ShoppingCart\Tests\Factories;

use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Models\CartItem;
use Yab\ShoppingCart\Tests\Models\Product;

$factory->define(CartItem::class, function () {
    $product = factory(Product::class)->create([
        'price' => 9.95,
    ]);
    return [
        'cart_id' => function () {
            return factory(Cart::class)->create()->id;
        },
        'purchaseable_id' => $product->id,
        'purchaseable_type' => $product->getMorphClass(),
        'qty' => 1,
        'price' => 9.95,
    ];
});
