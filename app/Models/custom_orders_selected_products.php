<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class custom_orders_selected_products extends Model
{
    use HasFactory;

    protected $fillable = ['custom_order_id','custom_orders_sellers_replies_id','quantity','price'];

    public function custom_order()
    {
        return $this->belongsTo(custom_orders::class,'custom_order_id');
    }

    public function reply()
    {
        return $this->belongsTo(custom_orders_sellers_reply::class,'custom_orders_sellers_replies_id');
    }
}
