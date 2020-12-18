<?php

namespace Yab\ShoppingCart\Tests\Feature\Api;

use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Tests\TestCase;
use Yab\ShoppingCart\Models\CartItem;
use Yab\ShoppingCart\Tests\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_new_cart_item_can_be_created_via_the_api()
    {
        $product = factory(Product::class)->create([
            'price' => 14.95,
        ]);

        $cart = factory(Cart::class)->create();
        
        $response = $this->post(route('checkout.items.store', [ $cart->id ]), [
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'options' => [ 'color' => 'green' ],
        ]);
    
        $response->assertSuccessful();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'unit_price' => 1495,
            'price' => 1495,
            'custom_fields' => json_encode([ 'options' => [ 'color' => 'green' ]], )
        ]);
    }

    /** @test */
    public function an_existing_cart_item_can_be_updated_via_the_api()
    {
        $product = factory(Product::class)->create([
            'price' => 24.95,
        ]);

        $item = factory(CartItem::class)->create([
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
        ]);
        $item->calculatePrice();
        $item->save();

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'cart_id' => $item->cart_id,
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'unit_price' => 2495,
            'price' => 2495,
        ]);

        $response = $this->put(route('checkout.items.update', [ $item->cart->id, $item->id ]), [
            'qty' => 2,
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'cart_id' => $item->cart_id,
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 2,
            'unit_price' => 2495,
            'price' => 4990,
        ]);
    }

    /** @test */
    public function an_existing_cart_item_can_be_removed_via_the_api()
    {
        $item = factory(CartItem::class)->create();

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
        ]);

        $response = $this->delete(route('checkout.items.destroy', [ $item->cart->id, $item->id ]));

        $response->assertSuccessful();

        $this->assertSoftDeleted('cart_items', [
            'id' => $item->id,
        ]);
    }
}
