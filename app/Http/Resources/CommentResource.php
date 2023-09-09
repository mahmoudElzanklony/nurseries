<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
          'comment'=>$this->comment,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'created_at'=>$this->created_at->format('Y m d, h:i A'),

        ];
    }
}
