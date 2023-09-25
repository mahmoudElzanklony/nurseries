<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
          'map_code'=>$this->map_code,
          'icon'=>ImagesResource::make($this->whenLoaded('image')),
          'created_at'=>$this->created_at,
        ];
    }
}
