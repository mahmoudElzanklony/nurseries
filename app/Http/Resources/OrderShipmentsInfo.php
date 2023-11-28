<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderShipmentsInfo extends JsonResource
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
          'ar_content'=>trans('keywords.'.$this->content),
          'content'=>$this->content,
          'status'=>$this->content,
          'created_at'=>$this->created_at,
        ];
    }
}
