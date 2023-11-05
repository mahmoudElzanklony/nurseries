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
                $price = $this->product->main_price;
                $features = orders_items_features::query()->where('order_item_id','=',$this->id)->sum('price') ?? 0;
                return $price + $features;
          }),
          'created_at'=>$this->created_at,
        ];
    }
}
