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

    public function image(){
        return $this->morphOne(images::class,'imageable');
    }

    public function likes(){
        return $this->hasMany(likes::class,'item_id');
    }

    public function last_four_likes(){
        return $this->hasManyThrough(User::class,likes::class,'user_id','id')->orderBy('id','DESC')->limit(4);
    }

    public function seen(){
        return $this->hasOne(seen::class,'item_id');
    }

    public function cares(){
        return $this->hasMany(products_care::class,'product_id');
    }

    public function user_care(){
        return $this->hasOne(users_products_cares::class,'product_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function category(){
        return $this->belongsTo(categories::class,'category_id');
    }

    public function favourite(){
        return $this->hasOne(favourites::class,'item_id')
            ->where('type','=','product')
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

    public function rates(){
        return $this->hasManyThrough(orders_items_rates::class,orders_items::class,'product_id','order_item_id');
    }

    public function orders_items(){
        return $this->hasMany(orders_items::class,'product_id');
    }

    public function deliveries(){
        return $this->hasMany(products_delivery::class,'product_id');
    }
}
