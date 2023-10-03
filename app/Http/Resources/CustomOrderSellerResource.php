<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderSellerResource extends JsonResource
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
          'seller'=>UserResource::make($this->seller),
          'order'=>CustomOrderResource::make($this->whenLoaded('order')),
          'reply'=>CustomOrderSellerReplyResource::make($this->whenLoaded('reply')),
          'created_at'=>$this->created_at,
        ];
    }
}
