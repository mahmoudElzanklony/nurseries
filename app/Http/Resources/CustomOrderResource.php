<?php

namespace App\Http\Resources;

use App\Models\custom_orders_sellers;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->status == 'accepted') {
            $accepted_seller_from_client = custom_orders_sellers::query()
                ->where('custom_order_id', '=', $this->id)
                ->where('status', '=', 'accepted')->whereHas('reply', function ($e) {
                    $e->where('client_reply', '=', 'client_reply');
                })->with('reply')->first();
        }else{
            $accepted_seller_from_client = null;
        }
        return [
           'id'=>$this->id,
           'client'=>UserResource::make($this->user),
           'name'=>$this->name,
           'status'=>$this->status,
           'ar_status'=>trans('keywords.'.$this->status),
           'accepted_date'=>$this->when($this->status == 'accepted' && isset($accepted_seller_from_client),function() use ($accepted_seller_from_client ){
               if($accepted_seller_from_client != null){
                    return $accepted_seller_from_client->reply->created_at;
               }else{
                   return '';
               }
           }),
           'delivery_date'=>$this->when($this->status == 'accepted' && isset($accepted_seller_from_client),function() use ($accepted_seller_from_client){
                if($accepted_seller_from_client != null){
                    return Carbon::parse($accepted_seller_from_client->reply->created_at)->addDays($accepted_seller_from_client->reply->days_delivery);
                }else{
                    return '';
                }
            }),
           'images'=>ImagesResource::collection($this->images),
           'pending_alerts'=>$this->when(auth()->check() && isset($this->has_pending),function(){
               return CustomOrderSellerResource::collection($this->whenLoaded('sellers_alerts'));
           }),
           'accepted_alerts'=>CustomOrderSellerResource::collection($this->whenLoaded('sellers_alerts')),
           'rejected_alerts'=>CustomOrderSellerResource::collection($this->whenLoaded('sellers_alerts')),
           'created_at'=>$this->created_at,
        ];
    }
}
