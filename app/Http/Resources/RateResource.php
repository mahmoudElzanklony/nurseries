<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
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
          'comment'=>$this->comment,
          'rate_product_info'=>$this->rate_product_info,
          'rate_product_services'=>$this->rate_product_services,
          'rate_product_delivery'=>$this->rate_product_delivery,
          'user'=>UserResource::make($this->whenLoaded('user')),
        ];
    }
}
