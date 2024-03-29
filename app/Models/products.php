<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class products extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id','category_id','ar_name','ar_description','en_name','en_description','plant_type','main_price','quantity','status'];

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

    public function scopeHasUser($query)
    {
        return $query->whereHas('user',function($e){
            $e->whereRaw('deleted_at is null');
        });
    }

    public function cares(){
        return $this->hasMany(products_care::class,'product_id')
            ->whereRaw('(type = "seller" '.(auth()->check() == true ? ' OR user_id =  '.auth()->id():'').' ) ');
    }

    public function time_alert(){
        return $this->hasOneThrough(users_products_care_alerts::class,products_care::class,'product_id','product_care_id');
    }

    public function user_care(){
        return $this->hasOne(users_products_cares::class,'product_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }

    public function category(){
        return $this->belongsTo(categories::class,'category_id')->withTrashed();
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

    public function last_order_item(){
        return $this->hasOne(orders_items::class,'product_id')->orderBy('id','DESC');
    }

    public function deliveries(){
        return $this->hasMany(products_delivery::class,'product_id');
    }

    public function changeable_prices(){
        return $this->hasMany(products_prices::class,'product_id');
    }

    public function problems(){
        return $this->hasMany(products_problems::class,'product_id');
    }
}
