<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Models\CartItem;
use Yab\ShoppingCart\Contracts\TaxLogistics as TaxLogisticsInterface;

class TaxLogisticsTest implements TaxLogisticsInterface
{
    /**
     * Get the tax rate for the purchaseable and specific cart item.
     *
     * @param mixed $purchaseable
     * @param \Yab\ShoppingCart\Models\CartItem $item
     *
     * @return int
     */
    public static function getTaxRate(mixed $purchaseable, CartItem $item) : int
    {
        return 18;
    }
}
