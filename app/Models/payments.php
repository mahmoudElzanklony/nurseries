<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payments extends Model
{
    use HasFactory;

    protected $fillable = ['paymentable_id','paymentable_type','visa_id','money','tax'];

    public function paymentable(){
        return $this->morphTo();
    }

    public function visa(){
        return $this->belongsTo(users_visa::class,'visa_id');
    }

    public function orders(){
        return $this->belongsTo(orders::class,'paymentable_id');
    }

    public function custom_orders(){
        return $this->belongsTo(custom_orders::class,'paymentable_id');
    }
}
