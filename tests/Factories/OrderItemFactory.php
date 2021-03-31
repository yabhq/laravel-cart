<?php

namespace Yab\ShoppingCart\Tests\Factories;

use Yab\ShoppingCart\Models\Order;
use Yab\ShoppingCart\Models\OrderItem;
use Yab\ShoppingCart\Tests\Models\Product;

$factory->define(OrderItem::class, function () {
    $product = factory(Product::class)->create([
        'price' => 9.95,
    ]);
    return [
        'order_id' => function () {
            return factory(Order::class)->create()->id;
        },
        'purchaseable_id' => $product->id,
        'purchaseable_type' => $product->getMorphClass(),
        'qty' => 1,
        'unit_price' => 9.95,
        'price' => 9.95,
    ];
});
