<?php

namespace App\Http\Resources;

use App\Actions\ManageTimeAlert;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCareResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $next_time = $this->when($this->next_time != null, function (){
            return $this->next_time->next_alert;
        });
        return [
          'id'=>$this->id,
          'product_id'=>$this->product_id,
          'user_id'=>$this->user_id,
          'care'=>CareResource::make($this->care),
          'time_number'=>$this->time_number,
          'time_type'=>$this->time_type,
          'type'=>$this->type,
          'current_time'=>Carbon::now(),
          'next_time_alert'=>$next_time,
          'remaining_time'=>$this->when($this->next_time != null, function (){
              return ManageTimeAlert::difference_between_two_times(now(),$this->next_time->next_alert,$this->time_type);
          }),
          'created_at'=>$this->created_at,
        ];
    }
}
