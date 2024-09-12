<?php

namespace App\Http\Resources;

use App\Models\packages;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaBankOrdersResource extends JsonResource
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
          //'visa_id'=>$this->visa_id,
          'money'=> $this->money,
            // i will show price of product only
          'items'=>$this->when(isset($this->paymentable->items) && sizeof($this->paymentable->items) > 0 ,function (){
              return SmallProduct::collection($this->paymentable->items);
          }),
          'name' => $this->when(isset($this->paymentable->name) && $this->paymentable->name != null, function () {
                return $this->paymentable->name;
          }),
          'package' => $this->when(isset($this->paymentable->package_id) && $this->paymentable->package_id != null, function () {
                return PackageResource::make(packages::query()->find($this->paymentable->package_id));
          }),
        ];
    }
}
