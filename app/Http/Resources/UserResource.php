<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
          'username'=>$this->username,
          'email'=>$this->email,
          'phone'=>$this->phone,
          'role'=>$this->whenLoaded('role'),
          'created_at'=>$this->created_at->format('Y m d, h:i A'),
        ];
    }
}
