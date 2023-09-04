<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favourites extends Model
{
    use HasFactory;

    protected $fillable =  ['user_id','product_id'];

    public function product(){
        return $this->belongsTo(products::class,'product_id');
    }
}
