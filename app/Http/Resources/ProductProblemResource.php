<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductProblemResource extends JsonResource
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
          'message'=>$this->message,
          'status'=>$this->status,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'product'=>ProductResource::make($this->whenLoaded('product')),
          'created_at'=>$this->created_at
        ];
    }
}
