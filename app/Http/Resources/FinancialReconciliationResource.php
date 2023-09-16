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
          'orders'=>OrderResource::collection($this->whenLoaded('orders')),
          'image'=>ImagesResource::make($this->whenLoaded('image')),
          'created_at'=>$this->created_at->format('Y m d, h:i A'),
        ];
    }
}
