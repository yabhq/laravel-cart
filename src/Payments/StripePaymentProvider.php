<?php

namespace Yab\ShoppingCart\Payments;

use Yab\ShoppingCart\Checkout;
use Yab\ShoppingCart\Contracts\PaymentProvider;
use Yab\ShoppingCart\Exceptions\PaymentFailedException;

class StripePaymentProvider implements PaymentProvider
{
    /**
     * Perform a charge based on the checkout total.
     *
     * @param \Yab\ShoppingCart\Checkout $checkout
     * @param array $chargeable
     *
     * @return void
     */
    public static function charge(Checkout $checkout, array $chargeable) : void
    {
        $stripe = new \Stripe\StripeClient(
            config('checkout.stripe.secret_key')
        );

        try {
            $response = $stripe->charges->create([
                'amount' => $checkout->getTotal() * 100, // Amount in cents
                'currency' => config('checkout.currency'),
                'source' => $chargeable['token'],
                'capture' => true,
            ]);
            if ($response->status !== 'succeeded') {
                throw new PaymentFailedException($e->getMessage());
            }
            $checkout->getCart()->saveReceipt($checkout, $response->id);
        } catch (\Exception $e) {
            throw new PaymentFailedException($e->getMessage());
        }
    }
}
