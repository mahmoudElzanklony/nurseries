<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cancelled_orders_items extends Model
{
    use HasFactory;

    protected $fillable = ['order_item_id','product_id','content','type'];

    public function product()
    {
        return $this->belongsTo(products::class,'product_id');
    }

    public function order_item(){
        return $this->belongsTo(orders_items::class,'order_item_id');
    }

    public function custom_order(){
        return $this->belongsTo(custom_orders::class,'order_item_id');
    }

    public function images(){
        return $this->morphMany(images::class,'imageable');
    }
}
