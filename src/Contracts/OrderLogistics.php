<?php

namespace Yab\ShoppingCart\Contracts;

use Yab\ShoppingCart\OrderFacade;

interface OrderLogistics
{
    public static function afterOrderPlaced(OrderFacade $order) : void;
}
