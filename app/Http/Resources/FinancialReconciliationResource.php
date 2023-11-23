<?php

namespace App\Http\Resources;

use App\Models\custom_orders;
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
          'user_id'=>$this->user_id,
          'seller_id'=>$this->seller_id,
          'total_money'=>$this->total_money,
          'admin_profit_percentage'=>$this->admin_profit_percentage,
          'seller_profit'=>$this->total_money - ($this->total_money * $this->admin_profit_percentage / 100),
          'admin_profit'=>$this->total_money * $this->admin_profit_percentage / 100,
          'ar_status'=>trans('keywords.'.$this->status),
          'status'=>$this->status,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'seller'=>UserResource::make($this->whenLoaded('seller')),
          'order'=>OrderResource::make($this->whenLoaded('orders')),
          'custom_orders'=>CustomOrderResource::make($this->whenLoaded('custom_orders')),
          'image'=>ImagesResource::make($this->whenLoaded('image')),
          'problem'=>FinancialreconciliationProblemResource::make($this->whenLoaded('problem')),
          'created_at'=>$this->created_at,
        ];
    }
}
