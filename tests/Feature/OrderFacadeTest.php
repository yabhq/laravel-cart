<?php

namespace Yab\ShoppingCart\Tests\Feature;

use Yab\ShoppingCart\OrderFacade;
use Yab\ShoppingCart\Models\Order;
use Yab\ShoppingCart\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderFacadeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_new_order_can_be_created()
    {
        $facade = OrderFacade::create();

        $this->assertTrue($facade instanceof OrderFacade);

        $this->assertDatabaseHas('orders', [
            'id' => $facade->getModel()->id,
        ]);
    }

    /** @test */
    public function an_order_can_be_retrieved_by_the_order_order_id()
    {
        $order = factory(Order::class)->create();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);

        $facade = OrderFacade::findById($order->id);

        $this->assertTrue($facade instanceof OrderFacade);
        $this->assertEquals($order->id, $facade->getModel()->id);
    }

    /** @test */
    public function an_order_can_be_destroyed()
    {
        $order = factory(Order::class)->create();

        $facade = OrderFacade::findById($order->id);
        $facade->destroy();

        $this->assertSoftDeleted('orders', [
            'id' => $order->id,
        ]);
    }

    /** @test */
    public function the_underlying_query_builder_for_an_order_can_be_retrieved()
    {
        $order = factory(Order::class)->create();

        $facade = new OrderFacade($order);

        $this->assertTrue($facade->getBuilder() instanceof Builder);

        $this->assertEquals(1, $facade->getBuilder()->count());
    }
}
