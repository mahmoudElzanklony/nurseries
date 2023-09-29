<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categories_features extends Model
{
    use HasFactory;

    protected $fillable = ['category_id','ar_name','en_name'];

    public function image(){
        return $this->morphOne(images::class,'imageable');
    }
}
