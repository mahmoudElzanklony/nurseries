<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_items_rates extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','order_item_id','comment','rate_product_info','rate_product_services','rate_product_delivery'];

    public function user(){
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }

    public function order_item()
    {
        return $this->belongsTo(orders_items::class,'order_item_id');
    }
}
