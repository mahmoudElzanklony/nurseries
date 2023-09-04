<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OperationResource extends JsonResource
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
          'branch_id'=>$this->branch_id,
          'status'=>$this->status,
          'direction'=>$this->direction,
          'last_time_create'=>$this->last_time_create,
          'last_time_update'=>$this->last_time_update,
          'created_at'=>$this->created_at->format('Y m d, h:i A'),
          'period'=>OperationRepeatResource::collection($this->period),
          'data'=>new OperationInfoResource($this->whenLoaded('database_tables_columns')),
          'conditions'=>new OperationConditionsResource($this->whenLoaded('conditions')),
          'transactions_count'=>$this->transactions_count,
          'last_transaction'=>$this->last_transaction,
        ];
    }
}
