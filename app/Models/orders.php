<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class orders extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id','seller_id','payment_method','address','has_coupon','seller_profit'];

    public function seller(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function shipments_info(){
        return $this->hasMany(orders_shipment_info::class,'order_id');
    }

    public function client(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function items(){
        return $this->hasMany(orders_items::class,'order_id');
    }
}
