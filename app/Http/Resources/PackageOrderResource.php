<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageOrderResource extends JsonResource
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
          'payment_type'=>$this->payment_type,
          'status'=>$this->status,
          'package'=>PackageResource::make($this->package),
          'user'=>UserResource::make($this->user),
        ];
    }
}
