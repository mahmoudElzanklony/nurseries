<?php

namespace App\Http\Resources;

use App\Models\payments;
use App\Models\taxes;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckCouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */



    public function toArray($request)
    {

        return [
            'id'=>$this->id,
            'product'=>ProductResource::make($this->product),
            'coupon'=>ProductCouponActivationResource::make($this->coupon),
            'quantity'=>$this->quantity,
            'prices_info'=>$this->when(true, function(){

                $tax_percen = taxes::query()->first()->percentage;


                $all_price_with_tax = round($this->price, 2);
                $all_price_without_tax = round($this->price / (1+$tax_percen/100),2);
                $price_item_with_tax = round($this->price / $this->quantity, 2);
                $price_item_without_tax = round($all_price_without_tax / $this->quantity, 2);
                return [
                    'all_price_with_tax'=>$all_price_with_tax,
                    'all_price_without_tax'=>$all_price_without_tax,
                    'price_item_with_tax'=>$price_item_with_tax,
                    'price_item_without_tax'=>$price_item_without_tax,
                ];
            }),

        ];
    }
}
