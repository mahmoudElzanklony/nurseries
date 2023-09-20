<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_care extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','product_id','care_id','time_number','time_type','type'];

    public function care(){
        return $this->belongsTo(care::class,'care_id');
    }

    public function product(){
        return $this->belongsTo(products::class,'product_id');
    }
}
