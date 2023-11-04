<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponProductsResource extends JsonResource
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
          'products_count'=>$this->when(isset($this->product),function(){
              return $this->products_count;
          }),
          'created_at'=>$this->when(isset($this->user),function(){
              return $this->created_at;
          }),
          'product'=>ProductResource::make($this->whenLoaded('product')),
          'user'=>UserResource::make($this->whenLoaded('user')),
        ];
    }
}
