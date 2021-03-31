<?php

namespace Yab\ShoppingCart\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $arr = [
            'subtotal' => $this->getSubtotal(),
            'cart' => $this->getModel(),
        ];

        if ($this->hasInfoNeededToCalculateTotal()) {
            $arr[] = $this->getCheckoutTotals();
        }

        return $arr;
    }

    /**
     * Get the shipping, discount, taxes and total for the checkout.
     *
     * @return array
     */
    private function getCheckoutTotals() : array
    {
        return [
            'shipping' => $this->getShipping(),
            'discount' => $this->getDiscount(),
            'taxes' => $this->getTaxes(),
            'total' => $this->getTotal(),
        ];
    }
}
