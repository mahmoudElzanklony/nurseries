<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RemoteDBResource extends JsonResource
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
          'driver'=>$this['db_connection'],
          'host'=>$this['db_host'],
          'port'=>$this['db_port'],
          'username'=>$this['db_username'],
          'password'=>$this['db_password'],
        ];
    }
}
