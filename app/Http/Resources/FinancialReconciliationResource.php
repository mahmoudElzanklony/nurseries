<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FinancialReconciliationResource extends JsonResource
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
          'total_money'=>$this->total_money,
          'admin_profit_percentage'=>$this->admin_profit_percentage,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'order'=>OrderResource::make($this->whenLoaded('order')),
          'image'=>ImagesResource::make($this->whenLoaded('image')),
          'created_at'=>$this->created_at,
        ];
    }
}
