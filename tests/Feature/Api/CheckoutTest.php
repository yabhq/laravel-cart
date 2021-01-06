<?php

namespace Yab\ShoppingCart\Tests\Feature\Api;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Tests\TestCase;
use Yab\ShoppingCart\Models\CartItem;
use Yab\ShoppingCart\Tests\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_new_checkout_can_be_created_via_the_api()
    {
        $response = $this->post(route('checkout.store'));
    
        $response->assertSuccessful();

        $cart = Cart::firstOrFail();

        $response->assertJson([
            'subtotal' => 0,
            'taxes' => 0,
            'total' => 0,
            'cart' => [
                'id' => $cart->id,
            ],
        ]);
    }

    /** @test */
    public function an_existing_checkout_can_be_retrieved_via_the_api()
    {
        $product = factory(Product::class)->create([
            'price' => 9.95,
        ]);

        $item = factory(CartItem::class)->create([
            'purchaseable_id' => $product->id,
            'purchaseable_type' => $product->getMorphClass(),
            'qty' => 1,
            'price' => 9.95,
        ]);

        $response = $this->get(route('checkout.show', [ $item->cart->id ]));
    
        $response->assertSuccessful();

        $response->assertJson([
            'subtotal' => 14.95,
            'taxes' => 2.69,
            'total' => 17.64,
            'cart' => [
                'id' => $item->cart->id,
                'items' => [
                    [
                        'id' => $item->id,
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function a_checkout_shipping_address_can_be_updated_via_the_api()
    {
        $cart = factory(Cart::class)->create();

        $address = [
            'street' => '123 Test Street',
            'city' => 'Toronto',
            'region' => 'ON',
            'postal_code' => 'L3L 3L3',
        ];

        $response = $this->put(route('checkout.update', [ $cart->id ]), [
            'custom_fields' => [
                'customer_info' => [],
                'shipping_address' => $address,
                'billing_address' => [],
            ],
        ]);
    
        $response->assertSuccessful();

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'custom_fields' => json_encode([
                'customer_info' => [],
                'shipping_address' => $address,
                'billing_address' => [],
            ]),
        ]);
    }

    /** @test */
    public function an_existing_checkout_can_be_deleted_via_the_api()
    {
        $cart = factory(Cart::class)->create();

        $response = $this->delete(route('checkout.destroy', [ $cart->id ]));
    
        $response->assertSuccessful();

        $this->assertSoftDeleted('carts', [
            'id' => $cart->id,
        ]);
    }

    /** @test */
    public function a_discount_can_be_applied_to_a_checkout_via_the_api()
    {
        $cart = factory(Cart::class)->create();

        $checkout = Checkout::findById($cart->id);

        $product = factory(Product::class)->create([
            'price' => 10,
        ]);

        $checkout->addItem(purchaseable: $product, qty: 1);

        $response = $this->post(route('checkout.discount', [ $cart->id ]), [
            'code' => '50OFF'
        ]);
    
        $response->assertSuccessful();

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'discount_code' => '50OFF',
            'discount_amount' => round($checkout->getSubtotal() * 0.5, 2) * 100,
        ]);
    }
}
