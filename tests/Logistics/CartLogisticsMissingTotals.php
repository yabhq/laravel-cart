<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Tests\Logistics\CartLogistics;
use Yab\ShoppingCart\Contracts\CartLogistics as CartLogisticsInterface;

class CartLogisticsMissingTotals extends CartLogistics implements CartLogisticsInterface
{
    /**
     * Determines if a checkout has all the information required to complete checkout.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     *
     * @return bool
     */
    public static function hasInfoNeededToCalculateTotal(Checkout $checkout) : bool
    {
        return false;
    }
}
