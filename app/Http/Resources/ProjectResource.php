<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
          'image'=>$this->image,
          'created_at'=>$this->created_at->format('Y m d, h:i A'),
          //'websites'=>WebsitesInfoCollection::make($this->whenLoaded('websites'))
          'websites'=>WebsitesInfoResource::collection($this->whenLoaded('websites')),
          'branches'=>BranchResource::collection($this->whenLoaded('branches')),
          'operations_count'=>$this->operations_count,
        ];
    }
}
