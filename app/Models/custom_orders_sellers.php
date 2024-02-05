<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class custom_orders_sellers extends Model
{
    use HasFactory;

    protected $fillable = ['custom_order_id','seller_id','status','client_reply'];

    public function seller(){
        return $this->belongsTo(User::class,'seller_id')->withTrashed();
    }

    public function reply(){
        return $this->hasMany(custom_orders_sellers_reply::class,'custom_orders_seller_id');
    }

    public function order(){
        return $this->belongsTo(custom_orders::class,'custom_order_id');
    }


}
