<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTempleteResource extends JsonResource
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
          'name'=>$this->name,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'notification_type'=>NotificationTypeResource::make($this->whenLoaded('notification_type')),
          'user_type'=>$this->user_type,
          'content'=>$this->content,
        ];
    }
}
