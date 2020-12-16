<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Models\CartItem;

interface TaxLogistics
{
    public static function getTaxRate(mixed $purchaseable, CartItem $item) : int;
}
