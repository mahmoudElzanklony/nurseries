<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteConfigDBResource extends JsonResource
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
            'website_id'=>$this->website_id,
            'db_driver'=>$this->db_driver,
            'db_name'=>$this->db_name,
            'db_host'=>$this->db_host,
            'db_port'=>$this->db_port,
            'db_username'=>$this->db_username,
            'db_password'=>$this->db_password,
        ];
    }
}
