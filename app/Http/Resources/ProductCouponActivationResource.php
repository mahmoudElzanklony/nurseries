<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCouponActivationResource extends JsonResource
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
          'before_apply_coupon'=>$this->total_price_before_apply,
          'coupon_cash_value'=>$this->coupon_value,
          'details'=>CouponRessource::make($this->coupon),
        ];
    }
}
