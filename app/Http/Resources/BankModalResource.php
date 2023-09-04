<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankModalResource extends JsonResource
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
            'date_of_transfer'=>$this->date_of_transfer,
            'number_sent_from'=>$this->number_sent_from,
            'number_sent_to'=>$this->number_sent_to,
            'image'=>$this->image,
            'created_at'=>$this->created_at->format('Y m d, h:i A'),

        ];
    }
}
