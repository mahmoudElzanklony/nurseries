<?php

namespace App\Http\Resources;

use App\Models\custom_orders;
use App\Models\orders;
use App\Models\rejected_financial_orders;
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
          'note'=>$this->note,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'seller'=>UserResource::make($this->whenLoaded('seller')),
          'orders'=>$this->when(true,function(){
              if($this->status != 'rejected'){
                  return OrderResource::collection($this->whenLoaded('orders'));
              }else{
                  $rej_orders = rejected_financial_orders::query()
                      ->where('financial_reconciliation_id','=',$this->id)
                      ->where('order_type','=','order')->select('order_id')->get()->map(function ($e){
                          return $e->order_id;
                      });

                  $orders = orders::query()->with('payment')
                      ->whereIn('id',$rej_orders)->get();
                  return OrderResource::collection($orders);
              }
          }),
          'custom_orders'=>$this->when(true,function(){
            if($this->status != 'rejected'){
                return CustomOrderResource::collection($this->whenLoaded('custom_orders'));
            }else{
                $rej_orders = rejected_financial_orders::query()
                    ->where('financial_reconciliation_id','=',$this->id)
                    ->where('order_type','=','custom_order')->select('order_id')->get()->map(function ($e){
                        return $e->order_id;
                    });
                $orders = custom_orders::query()->with('payment')->whereIn('id',$rej_orders)->get();
                return CustomOrderResource::collection($orders);
            }
          }),
          'image'=>ImagesResource::make($this->whenLoaded('image')),
          'problem'=>FinancialreconciliationProblemResource::make($this->whenLoaded('problem')),
          'created_at'=>$this->created_at,
        ];
    }
}
