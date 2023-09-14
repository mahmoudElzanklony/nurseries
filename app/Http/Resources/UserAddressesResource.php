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
          'default_address'=>$this->default_address,
          'area'=>AreaResource::make($this->whenLoaded('area')),
          'created_at'=>$this->created_at->format('Y m d, h:i A'),

        ];
    }
}
