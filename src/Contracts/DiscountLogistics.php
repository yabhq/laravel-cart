<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\Checkout;

interface DiscountLogistics
{
    public static function getDiscountFromCode(Checkout $checkout, string $code) : float;
}
