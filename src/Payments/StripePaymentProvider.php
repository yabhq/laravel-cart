<?php

namespace Yab\ShoppingCart\Payments;

use Stripe\StripeClient;
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
        $stripe = new StripeClient(config('checkout.stripe.secret_key'));

        try {
            $response = self::createCharge($stripe, $checkout, $chargeable);
            if ($response->status !== 'succeeded') {
                throw new PaymentFailedException('The charge was not successful');
            }
            $checkout->getCart()->saveReceipt($checkout, self::getReceipt($response));
        } catch (\Exception $e) {
            throw new PaymentFailedException($e->getMessage());
        }
    }

    /**
     * Create a new charge in Stripe and return the response object.
     *
     * @param \Stripe\StripeClient $stripe
     * @param \Yab\ShoppingCart\Checkout $checkout
     * @param array $chargeable
     *
     * @return object
     */
    private static function createCharge(StripeClient $stripe, Checkout $checkout, array $chargeable) : object
    {
        $payload = [
            'amount' => $checkout->getTotal() * 100, // Amount in cents
            'currency' => config('checkout.currency'),
            'source' => $chargeable['token'],
            'capture' => true,
        ];

        // TODO: Creation of Stripe customer to tie to charge
        // if (isset($chargeable['customer_id'])) {
        //     $payload['customer'] = $chargeable['customer_id'];
        // } else {
        //     $payload['customer'] = self::createCustomer($stripe, $chargeable)->id;
        // }

        return $stripe->charges->create($payload);
    }

    /**
     * Create a new Stripe customer for the charge.
     *
     * @param \Stripe\StripeClient $stripe
     * @param array $chargeable
     *
     * @return object
     */
    private static function createCustomer(StripeClient $stripe, array $chargeable) : object
    {
        return $stripe->customers->create();
    }

    /**
     * Get the receipt payload to save to the database.
     *
     * @param object $response
     *
     * @return array
     */
    private static function getReceipt(object $response) : array
    {
        return [
            'transaction_id' => $response->id,
            'customer_id' => $response->customer,
        ];
    }
}
