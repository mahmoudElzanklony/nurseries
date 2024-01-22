<?php

namespace App\Http\Resources;

use App\Actions\GetHighDeliveryDays;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderSellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->status == 'pending'){
            $status = 'pending_invitation';
            $ar_status = 'لم يتم الرد علي الدعوة';
        }else if($this->status == 'rejected'){
            $status = 'rejected_invitation';
            $ar_status = 'تم رفض الدعوة';
        }else if($this->status == 'accepted' && $this->client_reply == 'pending'){
            $status = 'pending_offer';
            $ar_status = 'لم يتم رد العميل';
        }else if($this->status == 'accepted' && $this->client_reply == 'accepted'){
            $status = 'accepted_offer';
            $ar_status = 'تم موافقة العميل علي عرضك';
        }else if($this->status == 'accepted' && $this->client_reply == 'rejected'){
            $status = 'rejected_offer';
            $ar_status = 'تم رفض عرضك من قبل العميل';
        }
        dd($this->status,$this->client_reply,$this->status == 'rejected');
        return [
          'id'=>$this->id,
          'seller'=>UserResource::make($this->seller),
          'order'=>CustomOrderResource::make($this->whenLoaded('order')),
          'reply'=>CustomOrderSellerReplyResource::collection($this->reply),
          'calc_max_delivery_with_days'=>GetHighDeliveryDays::get($this->reply),
          'status'=>$status ?? '',
         /* 'reply_status'=>$this->status,
          'client_reply'=>$this->client_reply,*/
          'ar_status'=>$ar_status ?? '',
          'created_at'=>$this->created_at,
        ];
    }
}
