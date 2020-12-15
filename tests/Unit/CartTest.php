<?php

namespace Yab\ShoppingCart\Tests\Unit;

use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_cart_can_be_created()
    {
        $cart = factory(Cart::class)->create();

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id
        ]);
    }
    
    /** @test */
    public function a_cart_can_be_soft_deleted()
    {
        $cart = factory(Cart::class)->create();

        $cart->delete();

        $this->assertSoftDeleted('carts', [
            'id' => $cart->id
        ]);
    }
}
