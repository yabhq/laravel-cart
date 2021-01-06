<?php

namespace Yab\ShoppingCart\Http\Controllers\Checkout;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Http\Controllers\Controller;
use Yab\ShoppingCart\Http\Resources\CheckoutResource;
use Yab\ShoppingCart\Http\Requests\CheckoutDiscountRequest;

class CheckoutDiscountController extends Controller
{
    /**
     * Apply a discount code to a checkout.
     *
     * @param  \Yab\ShoppingCart\Http\Requests\CheckoutDiscountRequest  $request
     * @param  string $checkoutId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutDiscountRequest $request, string $checkoutId)
    {
        $checkout = Checkout::findById($checkoutId);

        $checkout->applyDiscountCode($request->code);

        return new CheckoutResource($checkout);
    }
}
