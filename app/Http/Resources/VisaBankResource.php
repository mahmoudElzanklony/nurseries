<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VisaBankResource extends JsonResource
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
          'name'=>$this->name,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'card_number'=>$this->card_number,
          'end_date'=>$this->end_date,
          'cvv'=>$this->cvv,
          'orders'=>VisaBankOrdersResource::collection($this->orders) ?? [],
          'custom_orders'=>VisaBankOrdersResource::collection($this->custom_orders) ?? [],
          'created_at'=>$this->created_at,
        ];
    }
}
