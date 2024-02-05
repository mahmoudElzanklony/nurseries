<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class orders extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id','seller_id','payment_method','has_coupon','seller_profit','financial_reconciliation_id'];

    public function seller(){
        return $this->belongsTo(User::class,'seller_id')->withTrashed();
    }


    public function payment(){
        return $this->morphOne(payments::class,'paymentable');
    }

    public function products(){
        return $this->hasManyThrough(products::class,orders_items::class,'order_id','id');
    }

    public function shipments_info(){
        return $this->hasMany(orders_shipment_info::class,'order_id')->where('type','=','order');
    }

    public function last_shipment_info(){
        return $this->hasOne(orders_shipment_info::class,'order_id')->where('type','=','order')->latestOfMany();
    }

    public function client(){
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }

    public function items(){
        return $this->hasMany(orders_items::class,'order_id');
    }

    public function item(){
        return $this->hasOne(orders_items::class,'order_id');
    }

    public function coupon(){
        return $this->morphOne(users_coupons::class,'couponable');
    }

    public function address(){
        return $this->hasOne(orders_addresses::class,'order_id');
    }
}
