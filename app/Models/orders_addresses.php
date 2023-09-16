<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_addresses extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','user_address_id','delivery_price','days_delivery'];
}
