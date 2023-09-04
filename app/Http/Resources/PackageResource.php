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
          'info'=>$this->info,
          'price'=>$this->price,
          'no_points'=>$this->no_points,
          'expire_date'=>$this->expire_date,
          'image'=>'packages/'.($this->image != null ?$this->image:'default.png'),
        ];
    }
}
