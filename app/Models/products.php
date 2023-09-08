<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class products extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id','category_id','ar_name','ar_description','en_name','en_description','main_price','quantity'];

    public function images(){
        return $this->morphMany(images::class,'imageable');
    }

    public function category(){
        return $this->belongsTo(categories::class,'category_id');
    }

    public function favourite(){
        return $this->hasOne(favourites::class,'product_id')
            ->where('user_id',auth()->id());
    }

    public function wholesale_prices(){
        return $this->hasMany(products_wholesale_prices::class,'product_id')->orderBy('min_quantity','ASC');
    }

    public function discounts(){
        return $this->hasMany(products_discount::class,'product_id');
    }

    public function features(){
        return $this->hasMany(products_features_prices::class,'product_id');
    }

    public function answers(){
        return $this->hasMany(products_questions_answers::class,'product_id');
    }
}
