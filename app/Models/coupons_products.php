<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class coupons_products extends Model
{
    use HasFactory;

    protected $fillable = ['coupon_id','product_id'];

    public function product(){
        return $this->belongsTo(products::class,'product_id');
    }
}
