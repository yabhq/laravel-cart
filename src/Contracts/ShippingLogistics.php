<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Checkout;

interface ShippingLogistics
{
    public static function getShippingCost(Checkout $checkout) : float;
}
