<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderSellerReplyResource extends JsonResource
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
            'info'=>$this->info,
            'product_price'=>$this->product_price,
            'days_delivery'=>$this->days_delivery,
            'delivery_price'=>$this->delivery_price,
            'client_reply'=>trans('keywords.'.$this->client_reply),
            'reply_status'=>$this->client_reply == 'accepted' ? true:false,
            'custom_order_seller'=>CustomOrderSellerResource::make($this->whenLoaded('custom_order_seller')),
            'images'=>ImagesResource::collection($this->images),
            'created_at'=>$this->created_at,
        ];
    }
}
