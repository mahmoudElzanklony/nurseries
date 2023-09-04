<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmallDataWebsiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(isset($this['id'])) {
            return [
                'id' => $this['id'],
            ];
        }else if(isset($this['connection'])){
            return [
                'connection' => $this['connection'],
            ];
        }
    }
}
