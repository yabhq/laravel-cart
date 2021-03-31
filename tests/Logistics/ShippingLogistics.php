<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Contracts\ShippingLogistics as ShippingLogisticsInterface;

class ShippingLogistics implements ShippingLogisticsInterface
{
    /**
     * Get the shipping cost given the checkout instance.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     *
     * @return float
     */
    public static function getShippingCost(Checkout $checkout) : float
    {
        return 5 * $checkout->getModel()->items()->count();
    }
}
