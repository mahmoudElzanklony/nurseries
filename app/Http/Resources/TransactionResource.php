<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
          'first_website_id'=>$this->first_website_id,
          'second_website_id'=>$this->second_website_id,
          'created_at'=>$this->created_at->format('Y m d, h:i A'),
        ];
    }
}
