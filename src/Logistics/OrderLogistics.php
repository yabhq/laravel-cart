<?php

namespace App\Logistics;

use Yab\ShoppingCart\PurchaseOrder;
use Yab\ShoppingCart\Contracts\OrderLogistics as OrderLogisticsInterface;

class OrderLogistics implements OrderLogisticsInterface
{
    /**
     * Custom logic following an order successfully being placed.
     *
     * @param \Yab\ShoppingCart\PurchaseOrder $order
     *
     * @return void
     */
    public static function afterOrderPlaced(PurchaseOrder $order) : void
    {
        // Send the purchaser a nice email, or perform any other 
        // actions as needed here
    }
}
