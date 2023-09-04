<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OperationInfoResource extends JsonResource
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
            'website_id'=>$this->first()->load('website')->website->id,
            'connection'=>$this->first()->load('website')->website->connection,
            'structure'=>DBTablesColumnsResource::collection($this),
        ];
    }
}
