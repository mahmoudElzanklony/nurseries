<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDeliveriesResource extends JsonResource
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
          'location_id'=>$this->location_id,
          'city'=>CityResource::make($this->whenLoaded('city')),
          'location_type'=>$this->location_type,
          'price'=>$this->price,
          'days_delivery'=>$this->days_delivery,
        ];
    }
}
