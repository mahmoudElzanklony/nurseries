<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class custom_orders extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','address_id','name','status','financial_reconciliation_id'];

    public function sellers_alerts(){
        return $this->hasMany(custom_orders_sellers::class,'custom_order_id');
    }

    public function payment(){
        return $this->morphOne(payments::class,'paymentable');
    }

    public function pending_alerts(){
        return $this->hasMany(custom_orders_sellers::class,'custom_order_id')->whereRaw('status = "pending" or status is null');
    }

    public function accepted_alerts(){
        return $this->hasMany(custom_orders_sellers::class,'custom_order_id')->where('status','=','accepted');
    }

    public function rejected_alerts(){
        return $this->hasMany(custom_orders_sellers::class,'custom_order_id')->where('status','=','rejected');
    }


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function address(){
        return $this->belongsTo(user_addresses::class,'address_id');
    }


    public function images(){
        return $this->morphMany(images::class,'imageable');
    }
}
