<?php

namespace Yab\ShoppingCart;

use Illuminate\Database\Eloquent\Builder;
use Yab\ShoppingCart\Models\Order as OrderModel;

class Order
{
    /**
     * Create a new order instance for an order model.
     *
     * @param \Yab\ShoppingCart\Models\Order $order
     */
    public function __construct(protected OrderModel $order)
    {
    }

    /**
     * Find an order by an existing ID.
     *
     * @param string $id
     *
     * @return \Yab\ShoppingCart\Order
     */
    public static function findById(string $id) : Order
    {
        return new Order(OrderModel::findOrFail($id));
    }

    /**
     * Create a fresh new order with a new ID.
     *
     * @return \Yab\ShoppingCart\Order
     */
    public static function create() : Order
    {
        return new Order(OrderModel::create());
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
    public function getModel() : OrderModel
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
        return OrderModel::whereId($this->order->id);
    }
}