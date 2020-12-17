<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Models\Cart;

interface TaxLogistics
{
    public static function getTaxes(float $subtotal, float $shipping, Cart $cart) : float;
}
