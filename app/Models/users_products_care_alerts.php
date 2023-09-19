<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_products_care_alerts extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','product_care_id','next_alert'];

    public function product_care(){
        return $this->belongsTo(products_care::class,'product_care_id');
    }
}
