<?php

namespace App\Logistics;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Exceptions\PaymentFailedException;
use Yab\ShoppingCart\Contracts\CartLogistics as CartLogisticsInterface;

class CartLogistics implements CartLogisticsInterface
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
        return $type::find($id);
    }

    /**
     * Custom logic following a successful checkout attempt.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     *
     * @return void
     */
    public static function afterSuccessfulCheckout(Checkout $checkout) : void
    {
        // For example, you may want to create an order here
    }

    /**
     * Custom logic following a failed checkout attempt.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     * @param \Yab\ShoppingCart\Exceptions\PaymentFailedException $e
     *
     * @return void
     */
    public static function afterFailedCheckout(Checkout $checkout, PaymentFailedException $e) : void
    {
    }
}
