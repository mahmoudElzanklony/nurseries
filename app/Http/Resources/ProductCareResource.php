<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCareResource extends JsonResource
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
          'product_id'=>$this->product_id,
          'user_id'=>$this->user_id,
          'care'=>CareResource::make($this->whenLoaded('care')),
          'time_number'=>$this->time_number,
          'time_type'=>$this->time_type,
          'type'=>$this->type,
          'created_at'=>$this->created_at,
        ];
    }
}
