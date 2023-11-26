<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPackageResource extends JsonResource
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
          'user'=>UserResource::make($this->whenLoaded('user')),
          'package'=>PackageResource::make($this->whenLoaded('package')),
          'price'=>$this->price,
          'expiration_date'=>$this->expiration_date,
          'created_at'=>$this->created_at
        ];
    }
}
