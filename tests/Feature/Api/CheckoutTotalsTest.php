<?php

namespace Yab\ShoppingCart\Tests\Feature\Api;

use Illuminate\Http\Response;
use App\Logistics\CartLogistics;
use Yab\ShoppingCart\Models\Cart;
use Yab\ShoppingCart\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Yab\ShoppingCart\Payments\LocalPaymentProvider;
use Yab\ShoppingCart\Tests\Logistics\CartLogisticsMissingTotals;

class CheckoutTotalsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_checkout_charge_cannot_be_performed_if_checkout_missing_totals_info()
    {
        app()->bind(CartLogistics::class, CartLogisticsMissingTotals::class);
        
        Config::set('stripe.provider', LocalPaymentProvider::class);
        
        $cart = factory(Cart::class)->create();

        $response = $this->post(route('checkout.stripe', [ $cart->id ]), [
            'token' => 'tok_123456',
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $response->assertJson([
            'message' => 'The checkout is missing required information',
        ]);
    }
}
