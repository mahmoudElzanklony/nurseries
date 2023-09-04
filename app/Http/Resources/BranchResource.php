<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'status'=>$this->status,
            'project_id'=>$this->project_id,
            'created_at'=>$this->created_at->format('Y m d, h:i A'),
            'project'=>new ProjectResource($this->whenLoaded('project')),
            'operations'=>OperationResource::collection($this->whenLoaded('operations')),
            'transactions_count'=>$this->transactions_count,
            'last_transaction'=>new TransactionResource($this->whenLoaded('last_transaction')),
            'operations_count'=>$this->operations_count,

        ];
    }
}
