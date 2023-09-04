<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_store_info extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','type','address','business_phone','business_address'];
}
