<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CancelOrderItemResource extends JsonResource
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
          'order_item'=>OrderItemsResource::make($this->whenLoaded('order_item')),
          'product_id'=>TinyProductResource::make($this->whenLoaded('product')),
          'content'=>$this->content,
          'created_at'=>$this->created_at
        ];
    }
}
