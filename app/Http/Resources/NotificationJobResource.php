<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationJobResource extends JsonResource
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
          'template'=>NotificationTempleteResource::make($this->whenLoaded('template')),
          'type'=>NotificationTypeResource::make($this->whenLoaded('type')),
          'user_type'=>$this->user_type,
          'send_at'=>$this->send_at,
          'content'=>$this->content,
          'status'=>$this->status,
          'created_at'=>$this->created_at,
        ];
    }
}
