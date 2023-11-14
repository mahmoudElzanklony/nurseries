<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommercialInfoResource extends JsonResource
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
          'commercial_register'=>$this->commercial_register,
          'tax_card'=>$this->tax_card,
          'created_at'=>$this->created_at,
          'images'=>ImagesResource::collection($this->whenLoaded('images'))
        ];
    }
}
