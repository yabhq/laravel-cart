<?php

namespace Yab\ShoppingCart;

use Yab\ShoppingCart\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class OrderFacade
{
    /**
     * Create a new order instance for an order model.
     *
     * @param \Yab\ShoppingCart\Models\Order $order
     */
    public function __construct(protected Order $order)
    {
    }

    /**
     * Find an order by an existing ID.
     *
     * @param string $id
     *
     * @return \Yab\ShoppingCart\OrderFacade
     */
    public static function findById(string $id) : OrderFacade
    {
        return new OrderFacade(Order::findOrFail($id));
    }

    /**
     * Create a fresh new order with a new ID.
     *
     * @return \Yab\ShoppingCart\OrderFacade
     */
    public static function create() : OrderFacade
    {
        return new OrderFacade(Order::create());
    }

    /**
     * Destroy this order instance and soft delete the order model.
     *
     * @return void
     */
    public function destroy()
    {
        $this->order->delete();

        unset($this->order);
    }

    /**
     * Get the underlying order model for this order instance.
     *
     * @return \Yab\ShoppingCart\Models\Order
     */
    public function getModel() : Order
    {
        return $this->order->fresh();
    }

    /**
     * Get the underlying builder instance for the order.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder() : Builder
    {
        return Order::whereId($this->order->id);
    }
}