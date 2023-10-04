<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderResource extends JsonResource
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
           'client'=>UserResource::make($this->user),
           'name'=>$this->name,
           'status'=>trans('keywords.'.$this->status),
           'images'=>ImagesResource::collection($this->images),
           'sellers_alerts'=>CustomOrderSellerResource::collection($this->whenLoaded('sellers_alerts')),
           'created_at'=>$this->created_at,
        ];
    }
}