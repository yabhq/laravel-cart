<?php

namespace Yab\ShoppingCart\Http\Controllers\Checkout;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Http\Controllers\Controller;
use Yab\ShoppingCart\Http\Resources\CheckoutResource;
use Yab\ShoppingCart\Http\Requests\CheckoutStripeRequest;

class CheckoutStripeController extends Controller
{
    /**
     * Finalize a checkout using the Stripe payment provider.
     *
     * @param  \Yab\ShoppingCart\Http\Requests\CheckoutStripeRequest  $request
     * @param  string $checkoutId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutStripeRequest $request, string $checkoutId)
    {
        $checkout = Checkout::findById($checkoutId);

        $checkout->setPaymentProvider('stripe')->charge([ 'token' => $request->token ]);
        $checkout->getCart()->delete();

        return new CheckoutResource($checkout);
    }
}
