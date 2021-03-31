<?php

namespace App\Logistics;

use Yab\ShoppingCart\OrderFacade;
use Yab\ShoppingCart\Contracts\OrderLogistics as OrderLogisticsInterface;

class OrderLogistics implements OrderLogisticsInterface
{
    /**
     * Custom logic following an order successfully being placed.
     *
     * @param \Yab\ShoppingCart\OrderFacade $order
     *
     * @return void
     */
    public static function afterOrderPlaced(OrderFacade $order) : void
    {
        // Send the purchaser a nice email, or perform any other 
        // actions as needed here
    }
}
