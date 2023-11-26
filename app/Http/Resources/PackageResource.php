<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
          'description'=>$this->description,
          'price'=>$this->price,
          'type'=>$this->type,
          'user_count'=>$this->users_count,
          'features'=>PackageFeaturesResource::collection($this->whenLoaded('features')),
          'created_at'=>$this->created_at,
        ];
    }
}
