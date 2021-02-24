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
        return [
            'subtotal' => $this->getSubtotal(),
            $this->mergeWhen($this->hasInfoNeededToCalculateTotal(), [
                'shipping' => $this->getShipping(),
                'discount' => $this->getDiscount(),
                'taxes' => $this->getTaxes(),
                'total' => $this->getTotal(),
            ]),
            'cart' => $this->getCart(),
        ];
    }
}
