<?php

namespace App\Http\Resources;

use App\Models\financial_reconciliations;
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
          'payment_method'=>$this->payment_method,
          'has_coupon'=>$this->has_coupon == 0 ? false:true,
          'seller_profit'=>$this->seller_profit == 0 ? false:true,
          'items_price'=>round(doubleval($this->total_items),2),
          'address'=>$this->address,
          'client'=>UserResource::make($this->whenLoaded('client')),
          'seller'=>UserResource::make($this->whenLoaded('seller')),
          'payment'=>PaymentResource::make($this->whenLoaded('payment')),
          'items'=>OrderItemsResource::collection($this->whenLoaded('items')),
          'shipments_info'=>OrderShipmentsInfo::collection($this->whenLoaded('shipments_info')),
          'financial'=>$this->when($this->financial_reconciliation_id != null && auth()->user()->role->name == 'admin',function($e){
              return FinancialReconciliationResource::make(financial_reconciliations::query()->find($this->financial_reconciliation_id));
          }) ,
          'status'=>isset($this->shipments_info) && sizeof($this->shipments_info)  > 0 ? $this->shipments_info[sizeof($this->shipments_info) - 1]->content:'pending',
          'created_at'=>$this->created_at,

        ];
    }
}
