<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
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
          'product'=>ProductResource::make($this->whenLoaded('product')),
          'features'=>OrderItemsFeaturesResource::collection($this->whenLoaded('features')),
          'quantity'=>$this->quantity,
          'price'=>doubleval($this->price),
          'created_at'=>$this->created_at,

        ];
    }
}
