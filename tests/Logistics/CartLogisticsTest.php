<?php

namespace Yab\ShoppingCart\Tests\Logistics;

use Yab\ShoppingCart\Tests\Models\Product;
use Yab\ShoppingCart\Contracts\CartLogistics as CartLogisticsInterface;

class CartLogisticsTest implements CartLogisticsInterface
{
    /**
     * Get the purchaseable entity given the type and ID.
     *
     * @param string $type
     * @param mixed $id
     *
     * @return mixed
     */
    public static function getPurchaseable(string $type, mixed $id) : mixed
    {
        return Product::findOrFail($id);
    }
    
    /**
     * Get the tax rate for the purchaseable item given the shipping address.
     *
     * @param mixed $purchaseable
     * @param array $address
     *
     * @return int
     */
    public static function getTaxRate(mixed $purchaseable, array $address) : int
    {
        return 18;
    }
}
