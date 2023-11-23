<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponRessource extends JsonResource
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
          'name'=>$this->{app()->getLocale().'_name'},
          'code'=>$this->code,
          'number'=>$this->number,
          'discount'=>$this->discount,
          'end_date'=>$this->end_date,
          'created_at'=>$this->created_at,
          'status'=>$this->end_date == null ? true:(Carbon::parse($this->end_date) >= Carbon::now() ? true:false),
          'statistics'=>CouponProductsResource::collection($this->whenLoaded('order_items')),
          'products'=>TinyProductResource::collection($this->whenLoaded('products')),
          'users'=>CouponProductsResource::collection($this->whenLoaded('users')),
          'users_count'=>$this->when(isset($this->users_count),function(){
              return $this->users_count;
          })
        ];
    }
}
