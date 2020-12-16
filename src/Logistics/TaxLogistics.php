<?php

namespace App\Logistics;

use Yab\ShoppingCart\Models\CartItem;
use Yab\ShoppingCart\Contracts\TaxLogistics as TaxLogisticsInterface;

class TaxLogistics implements TaxLogisticsInterface
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
        // You may use $item->shipping_address here if needed
        return 0;
    }
}
