<?php

namespace App\Http\Resources;

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
        if($this->reply_status == 'pending' || $this->reply_status == null){
            $status = 'pending_invitation';
            $ar_status = 'لم يتم الرد علي الدعوة';
        }else if($this->reply_status == 'rejected'){
            $status = 'rejected_invitation';
            $ar_status = 'تم رفض الدعوة';
        }else if($this->reply_status == 'accepted' && $this->client_reply == 'pending'){
            $status = 'pending_offer';
            $ar_status = 'لم يتم رد العميل';
        }else if($this->reply_status == 'accepted' && $this->client_reply == 'accepted'){
            $status = 'accepted_offer';
            $ar_status = 'تم موافقة العميل علي عرضك';
        }else if($this->reply_status == 'accepted' && $this->client_reply == 'rejected'){
            $status = 'rejected_offer';
            $ar_status = 'تم رفض عرضك من قبل العميل';
        }
        return [
          'id'=>$this->id,
          'seller'=>UserResource::make($this->seller),
          'order'=>CustomOrderResource::make($this->whenLoaded('order')),
          'reply'=>CustomOrderSellerReplyResource::collection($this->reply),
          'status'=>$status ?? '',
          'ar_status'=>$ar_status ?? '',
          'client_reply'=>$this->client_reply.'_offer',
          'created_at'=>$this->created_at,
        ];
    }
}
