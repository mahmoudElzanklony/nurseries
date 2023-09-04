<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebsitesInfoResource extends JsonResource
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
            'project_id'=>$this->project_id,
            'url'=>$this->url,
            'connection'=>$this->connection,
            'data'=> $this->db_config != null ? new WebsiteConfigDBResource($this->whenLoaded('db_config')):new WebsiteConfigApiResource($this->whenLoaded('api_config'))
        ];
    }
}
