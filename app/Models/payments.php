<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payments extends Model
{
    use HasFactory;

    protected $fillable = ['paymentable_id','paymentable_type','visa_id','money'];

    public function paymentable(){
        return $this->morphTo();
    }

    public function visa(){
        return $this->belongsTo(users_visa::class,'visa_id');
    }
}
