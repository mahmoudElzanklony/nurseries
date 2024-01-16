<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SelectedProductsResource extends JsonResource
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
          'quantity'=>$this->quantity,
          'price'=>$this->price,
          'item_price'=>round($this->price / $this->quantity,2),
          'details'=>CustomOrderSellerReplyResource::make($this->whenLoaded('reply')),
          'created_at'=>$this->created_at,
        ];
    }
}
