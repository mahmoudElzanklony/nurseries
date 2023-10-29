<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_shipment_info extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','order_id','content'];

    public function order(){
        return $this->belongsTo(orders::class,'order_id');
    }
}
