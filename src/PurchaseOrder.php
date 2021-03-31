<?php

namespace Yab\ShoppingCart;

use Yab\ShoppingCart\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class PurchaseOrder
{
    /**
     * Create a new order instance for an order model.
     *
     * @param \Yab\ShoppingCart\Models\Order $model
     */
    public function __construct(protected Order $model)
    {
    }

    /**
     * Find an order by an existing ID.
     *
     * @param string $id
     *
     * @return \Yab\ShoppingCart\PurchaseOrder
     */
    public static function findById(string $id) : PurchaseOrder
    {
        return new PurchaseOrder(Order::findOrFail($id));
    }

    /**
     * Create a fresh new order with a new ID.
     *
     * @return \Yab\ShoppingCart\PurchaseOrder
     */
    public static function create() : PurchaseOrder
    {
        return new PurchaseOrder(Order::create());
    }

    /**
     * Create a new order for a particular checkout.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     * 
     * @return \Yab\ShoppingCart\PurchaseOrder
     */
    public static function createForCheckout(Checkout $checkout) : PurchaseOrder
    {
        $order = $checkout->getModel()->createOrder(
            subtotal: $checkout->getSubtotal(),
            shipping: $checkout->getShipping(),
            taxes: $checkout->getTaxes(),
            discount: $checkout->getDiscount(),
            total: $checkout->getTotal()
        );

        return new PurchaseOrder($order->fresh());
    }

    /**
     * Destroy this order instance and soft delete the order model.
     *
     * @return void
     */
    public function destroy()
    {
        $this->model->delete();

        unset($this->model);
    }

    /**
     * Get the underlying order model for this order instance.
     *
     * @return \Yab\ShoppingCart\Models\Order
     */
    public function getModel() : Order
    {
        return $this->model->fresh();
    }

    /**
     * Get the underlying builder instance for the order.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder() : Builder
    {
        return Order::whereId($this->model->id);
    }
}