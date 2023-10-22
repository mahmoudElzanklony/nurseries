<?php

namespace App\Http\Resources;

use App\Models\orders_items_features;
use Illuminate\Http\Resources\Json\JsonResource;

class SmallProduct extends JsonResource
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
          'id'=>$this->product->id,
          'name'=>$this->product->{app()->getLocale().'_name'},
          'price'=>$this->when(true,function (){
                $price = $this->price;
                $features = orders_items_features::query()->where('order_item_id','=',$this->id)->count('price') ?? 0;
                return $price + $features;
          }),
          'created_at'=>$this->created_at,
        ];
    }
}
