<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressesResource extends JsonResource
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
          'user_id'=>$this->id,
          'address'=>$this->address,
          'longitude'=>$this->longitude,
          'latitude'=>$this->latitude,
          'default_address'=>$this->default_address == 1 ? true:false,
          'created_at'=>$this->created_at,

        ];
    }
}
