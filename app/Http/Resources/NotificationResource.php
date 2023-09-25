<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
          'sender'=>UserResource::make($this->sender),
          'content'=>$this->{app()->getLocale().'_content'},
          'url'=>$this->url,
          'seen'=>$this->seen,
          'created_at'=>$this->created_at,
        ];
    }
}
