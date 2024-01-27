<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class custom_orders_shipment_info extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','order_id','content','type'];

    public function custom_order(){
        return $this->belongsTo(orders::class,'order_id')->where('type','=','custom_order');
    }
}
