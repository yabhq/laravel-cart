<?php

namespace App\Logistics;

use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Contracts\ShippingLogistics as ShippingLogisticsInterface;

class ShippingLogistics implements ShippingLogisticsInterface
{
    /**
     * Get the shipping cost given the cart instance.
     *
     * @param \Yab\ShoppingCart\Models\Cart $cart
     *
     * @return float
     */
    public static function getShippingCost(Cart $cart) : float
    {
        return 0;
    }
}
