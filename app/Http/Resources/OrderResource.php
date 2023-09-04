<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
          'price'=>$this->price,
          'user_id'=>$this->user_id,
          'payment_type'=>$this->payment_type,
          'current_points'=>$this->current_points,
          'status'=>$this->status,
          'package'=>PackageResource::make($this->package),
          'user'=>$this->whenLoaded('user'),
          'bank_modal'=>($this->bank_modal != null && ($this->payment_type == 'mobile' || $this->payment_type == 'bank')) ?
              BankModalResource::make($this->bank_modal):null,
          'created_at'=>$this->created_at->format('Y m d, h:i A'),

        ];
    }
}
