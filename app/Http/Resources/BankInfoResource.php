<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankInfoResource extends JsonResource
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
            'owner_name'=>$this->owner_name,
            'bank_name'=>$this->bank_name,
            'bank_account'=>$this->bank_account,
            'bank_iban'=>$this->bank_iban,
            'created_at'=>$this->created_at,
        ];
    }
}
