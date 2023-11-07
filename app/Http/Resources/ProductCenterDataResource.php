<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCenterDataResource extends JsonResource
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
          'description'=>$this->{app()->getLocale().'_description'},
          //'information'=>json_decode($this->data,true),
          'information'=>CenterProductInfoResource::make((object)(json_decode($this->data,true)))
        ];
    }
}
