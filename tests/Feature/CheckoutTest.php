<?php

namespace Yab\ShoppingCart\Tests\Feature;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Yab\ShoppingCart\Models\CartItem;
use Illuminate\Database\Eloquent\Builder;
use Yab\ShoppingCart\Events\CartItemAdded;
use Yab\ShoppingCart\Tests\Models\Product;
use Yab\ShoppingCart\Tests\Models\Customer;
use Yab\ShoppingCart\Events\CartItemDeleted;
use Yab\ShoppingCart\Events\CartItemUpdated;
use Yab\ShoppingCart\Tests\Models\NonPurchaser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Yab\ShoppingCart\Tests\Models\NonPurchaseable;
use Yab\ShoppingCart\Exceptions\PurchaserInvalidException;
use Yab\ShoppingCart\Exceptions\ItemNotPurchaseableException;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_new_checkout_can_be_created()
    {
        $checkout = Checkout::create();

        $this->assertTrue($checkout instanceof Checkout);

        $this->assertDatabaseHas('carts', [
            'id' => $checkout->getCart()->id,
        ]);
    }

    /** @test */
    public function a_checkout_can_be_retrieved_by_the_cart_id()
    {
        $cart = factory(Cart::class)->create();

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
        ]);

        $checkout = Checkout::findById($cart->id);

        $this->assertTrue($checkout instanceof Checkout);
        $this->assertEquals($cart->id, $checkout->getCart()->id);
    }

    /** @test */
    public function a_checkout_can_be_destroyed()
    {
        $cart = factory(Cart::class)->create();

        $checkout = Checkout::findById($cart->id);
        $checkout->destroy();

        $this->assertSoftDeleted('carts', [
            'id' => $cart->id,
        ]);
    }

    /** @test */
    public function the_underlying_query_builder_for_a_checkout_can_be_retrieved()
    {
        $cart = factory(Cart::class)->create();

        $checkout = new Checkout($cart);

        $this->assertTrue($checkout->getCartBuilder() instanceof Builder);

        $this->assertEquals(1, $checkout->getCartBuilder()->count());
    }

    /** @test */
    public function a_purchaseable_item_can_be_added_to_the_cart()
    {
        Event::fake([
            CartItemAdded::class
        ]);
        
        $checkout = Checkout::create();

        $product = factory(Product::class)->create([
            'price' => 9.95,
        ]);

        $item = $checkout->addItem($product, 1);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $checkout->getCart()->id,
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'unit_price' => 995,
            'price' => 995,
        ]);

        Event::assertDispatched(function (CartItemAdded $event) use ($item) {
            return $event->item->id === $item->id;
        });
    }

    /** @test */
    public function a_product_retail_price_can_be_manually_overriden()
    {
        $checkout = Checkout::create();

        $product = factory(Product::class)->create([
            'price' => 9.95,
        ]);

        $item = $checkout->addItem($product, 1, 13.95);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'cart_id' => $checkout->getCart()->id,
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'unit_price' => 1395,
            'price' => 1395,
        ]);
    }

    /** @test */
    public function custom_options_can_be_added_to_a_checkout_item()
    {
        $checkout = Checkout::create();

        $product = factory(Product::class)->create();

        $item = $checkout->addItem($product, 1, null, [ 'size' => 'medium' ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'cart_id' => $checkout->getCart()->id,
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'custom_fields' => json_encode([ 'options' => [ 'size' => 'medium' ]]),
        ]);
    }

    /** @test */
    public function adding_a_non_purchaseable_item_throws_an_exception()
    {
        $checkout = Checkout::create();

        $product = factory(NonPurchaseable::class)->create([
            'price' => 9.95,
        ]);

        $this->expectException(ItemNotPurchaseableException::class);

        $checkout->addItem($product, 1);
    }

    /** @test */
    public function an_existing_cart_item_can_be_updated()
    {
        Event::fake([
            CartItemUpdated::class
        ]);

        $product = factory(Product::class)->create([
            'price' => 9.95,
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
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'unit_price' => 995,
            'price' => 995,
        ]);

        $checkout = Checkout::findById($item->cart->id);

        $item = $checkout->updateItem($item->id, 2);

        $this->assertEquals(2, $item->qty);
        $this->assertEquals(19.90, $item->price);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 2,
            'unit_price' => 995,
            'price' => 1990,
        ]);

        Event::assertDispatched(function (CartItemUpdated $event) use ($item) {
            return $event->item->id === $item->id;
        });
    }

    /** @test */
    public function an_existing_cart_item_can_be_removed()
    {
        Event::fake([
            CartItemDeleted::class
        ]);
        
        $item = factory(CartItem::class)->create();

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
        ]);

        $checkout = Checkout::findById($item->cart->id);
        $checkout->removeItem($item->id);

        $this->assertSoftDeleted('cart_items', [
            'id' => $item->id,
        ]);

        Event::assertDispatched(function (CartItemDeleted $event) use ($item) {
            return $event->item->id === $item->id;
        });
    }

    /** @test */
    public function the_cart_shipping_costs_can_be_retrieved()
    {
        $productOne = factory(Product::class)->create([
            'price' => 100,
        ]);

        $cart = factory(Cart::class)->create();
        $checkout = new Checkout($cart);

        $checkout->addItem($productOne, 1);

        $this->assertEquals(5, $checkout->getShipping());
    }

    /** @test */
    public function the_cart_subtotal_can_be_retrieved()
    {
        $productOne = factory(Product::class)->create([
            'price' => 50,
        ]);

        $productTwo = factory(Product::class)->create([
            'price' => 25,
        ]);

        $checkout = Checkout::create();

        $checkout->addItem($productOne, 1);
        $checkout->addItem($productTwo, 2);

        // $100 (item cost) + 2 x $5 (shipping)
        $this->assertEquals(110, $checkout->getSubtotal());
    }

    /** @test */
    public function the_cart_taxes_can_be_retrieved()
    {
        $productOne = factory(Product::class)->create([
            'price' => 100,
        ]);

        $cart = factory(Cart::class)->create();
        $checkout = new Checkout($cart);

        $checkout->addItem($productOne, 1);

        // $100 (item cost) + $5 (shipping) = $105
        // $105 x 0.18 = $18.90
        $this->assertEquals(18.90, $checkout->getTaxes());
    }

    /** @test */
    public function the_cart_total_can_be_retrieved()
    {
        $productOne = factory(Product::class)->create([
            'price' => 100,
        ]);

        $cart = factory(Cart::class)->create();
        $checkout = new Checkout($cart);

        $checkout->addItem($productOne, 1);

        // $100 (item cost) + $5 (shipping cost) = $105
        // $105 x 0.18 = $18.90
        // $105 + $18.90 = $123.90
        $this->assertEquals(123.90, $checkout->getTotal());
    }

    /** @test */
    public function a_purchaser_can_be_set_for_the_checkout()
    {
        $cart = factory(Cart::class)->create();
        $checkout = new Checkout($cart);

        $customer = factory(Customer::class)->create();
        $checkout->setPurchaser($customer);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'purchaser_id' => $customer->id,
            'purchaser_type' => $customer->getMorphClass(),
        ]);
    }

    /** @test */
    public function a_non_purchaser_cannot_be_added_to_the_checkout()
    {
        $cart = factory(Cart::class)->create();
        $checkout = new Checkout($cart);

        $this->expectException(PurchaserInvalidException::class);

        $nonPurchaser = factory(NonPurchaser::class)->create();
        $checkout->setPurchaser($nonPurchaser);

        $this->assertDatabaseMissing('carts', [
            'id' => $cart->id,
            'purchaser_id' => $customer->id,
            'purchaser_type' => $customer->getMorphClass(),
        ]);
    }

    /** @test */
    public function a_discount_can_be_applied_to_the_checkout()
    {
        $productOne = factory(Product::class)->create([
            'price' => 100,
        ]);

        $cart = factory(Cart::class)->create();
        $checkout = new Checkout($cart);

        $checkout->addItem($productOne, 1);

        // $100 (item cost) + $5 (shipping cost) = $105
        // $105 x 0.18 = $18.90
        // $105 + $18.90 = $123.90
        $this->assertEquals(123.90, $checkout->getTotal());

        $checkout->applyDiscountCode('BADCODE'); // Won't work
        $this->assertEquals(123.90, $checkout->getTotal());

        $checkout->applyDiscountCode('50OFF'); // Gives 50% off
        $this->assertEquals(61.95, $checkout->getTotal());
    }
}
