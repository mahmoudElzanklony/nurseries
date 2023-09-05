<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDiscountsResource extends JsonResource
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
          'discount'=>$this->discount,
          'start_date'=>$this->start_date,
          'end_date'=>$this->end_date,
          'created_at'=>$this->created_at->format('Y m d, h:i A'),

        ];
    }
}
