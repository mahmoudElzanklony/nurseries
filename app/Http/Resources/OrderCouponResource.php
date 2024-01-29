<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCouponResource extends JsonResource
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
            'data' => CheckCouponResource::collection(json_decode(json_encode($this['data']))),
            'coupon' => CouponRessource::make(json_decode(json_encode($this['coupon']))),
        ];
    }
}
