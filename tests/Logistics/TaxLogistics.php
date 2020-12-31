<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Contracts\TaxLogistics as TaxLogisticsInterface;

class TaxLogistics implements TaxLogisticsInterface
{
    /**
     * Get the taxes given the checkout instance.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     *
     * @return float
     */
    public static function getTaxes(Checkout $checkout) : float
    {
        return round(($checkout->getSubtotal() - $checkout->getDiscount()) * 0.18, 2);
    }
}
