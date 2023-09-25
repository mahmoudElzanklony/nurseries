<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersProductsCareAlertsResource extends JsonResource
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
          'product_care'=>ProductCareResource::make($this->whenLoaded('product_care')),
          'next_alert'=>$this->next_alert,
          'created_at'=>$this->created_at,

        ];
    }
}
