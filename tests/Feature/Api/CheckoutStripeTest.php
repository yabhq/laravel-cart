<?php

namespace Yab\ShoppingCart\Tests\Feature\Api;

use Illuminate\Http\Response;
use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Yab\ShoppingCart\Payments\LocalPaymentProvider;
use Yab\ShoppingCart\Payments\FailedPaymentProvider;
class CheckoutStripeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_failed_stripe_checkout_returns_an_error_response()
    {
        Config::set('stripe.provider', FailedPaymentProvider::class);
        
        $cart = factory(Cart::class)->create();

        $response = $this->post(route('checkout.stripe', [ $cart->id ]), [
            'token' => 'tok_123456',
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJson([
            'message' => 'There was a problem processing the payment',
        ]);
    }

    /** @test */
    public function a_successful_stripe_checkout_returns_a_successful_response()
    {
        Config::set('stripe.provider', LocalPaymentProvider::class);
        
        $cart = factory(Cart::class)->create();

        $response = $this->post(route('checkout.stripe', [ $cart->id ]), [
            'token' => 'tok_123456',
        ]);

        $response->assertSuccessful();
    }
}
