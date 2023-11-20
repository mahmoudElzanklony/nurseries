<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_problems extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','product_id','message','status'];

    public function product(){
        return $this->belongsTo(products::class,'product_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function images(){
        return $this->morphMany(images::class,'imageable');
    }
}
