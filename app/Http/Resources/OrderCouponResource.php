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
        $data = $this->resource['data'] ?? null; // Access 'data' key from the $final_result array
        $coupon = $this->resource['coupon'] ?? null; // Access 'coupon' key from the $final_result array

        // Use $data and $coupon as needed in your response
        // ...

        return [
            'data' => $data,
            'coupon' => $coupon,
        ];
    }
}
