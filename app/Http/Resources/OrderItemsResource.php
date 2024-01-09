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
          'rate'=>RateResource::make($this->whenLoaded('rate')),
          'features'=>OrderItemsFeaturesResource::collection($this->whenLoaded('features')),
          'quantity'=>$this->quantity,
          'price'=>doubleval($this->price),
          'products_count'=>$this->when(isset($this->products_count),function(){
              return $this->products_count;
          }),
          'created_at'=>$this->created_at,

        ];
    }
}
