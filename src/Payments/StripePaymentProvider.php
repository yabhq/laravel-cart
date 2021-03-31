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
            $checkout->getModel()->saveReceipt($checkout, self::getReceipt($response));
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
            'capture' => true,
        ];

        $source = self::createSource($stripe, $chargeable['token'])->id;
        $customer = self::createCustomer($stripe, $source, $chargeable['email'] ?? '');
        
        $payload['customer'] = $customer->id;

        return $stripe->charges->create($payload);
    }

    /**
     * Create a new Stripe customer for the upcoming charge.
     *
     * @param \Stripe\StripeClient $stripe
     * @param string $source
     * @param string $email
     *
     * @return object
     */
    private static function createCustomer(StripeClient $stripe, string $source, string $email) : object
    {
        return $stripe->customers->create([
            'source' => $source,
            'email' => $email
        ]);
    }

    /**
     * Create a new Stripe source for the tokenized card data.
     *
     * @param \Stripe\StripeClient $stripe
     * @param string $token
     *
     * @return object
     */
    private static function createSource(StripeClient $stripe, string $token) : object
    {
        return $stripe->sources->create([
            'type' => 'card',
            'token' => $token,
        ]);
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
