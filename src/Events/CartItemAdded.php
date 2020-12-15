<?php

namespace Yab\ShoppingCart\Events;

use Yab\ShoppingCart\Models\CartItem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CartItemAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param \Yab\ShoppingCart\Models\CartItem $item
     *
     * @return void
     */
    public function __construct(public CartItem $item)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
