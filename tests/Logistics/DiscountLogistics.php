<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Contracts\DiscountLogistics as DiscountLogisticsInterface;

class DiscountLogistics implements DiscountLogisticsInterface
{
    /**
     * Get the discount amount for the checkout given the provided
     * discount code.
     *
     * Note: This should always return the **dollar amount** to discount
     * from the checkout, even if a percentage code is applied.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     * @param string $code
     *
     * @return float
     */
    public static function getDiscountFromCode(Checkout $checkout, string $code) : float
    {
        if ($code !== '50OFF') {
            return 0;
        }
        
        return round($checkout->getSubtotal() * 0.5, 2);
    }
}
