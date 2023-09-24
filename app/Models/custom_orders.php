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

    public function address(){
        return $this->belongsTo(user_addresses::class,'address_id');
    }


    public function images(){
        return $this->morphMany(images::class,'imagable');
    }
}
