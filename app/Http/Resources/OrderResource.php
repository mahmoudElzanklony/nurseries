<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
          'payment_method'=>$this->payment_method,
          'has_coupon'=>$this->has_coupon,
          'seller_profit'=>$this->seller_profit,
          'items_price'=>$this->total_items,
          'address'=>$this->address,
          'client'=>UserResource::make($this->whenLoaded('client')),
          'seller'=>UserResource::make($this->whenLoaded('seller')),
          'items'=>OrderItemsResource::collection($this->whenLoaded('items')),
          'shipments_info'=>OrderShipmentsInfo::collection($this->whenLoaded('shipments_info')),
          'created_at'=>$this->created_at->format('Y m d, h:i A'),

        ];
    }
}
