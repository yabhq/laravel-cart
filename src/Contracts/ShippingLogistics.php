<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Models\Cart;

interface ShippingLogistics
{
    public static function getShippingCost(Cart $cart) : float;
}
