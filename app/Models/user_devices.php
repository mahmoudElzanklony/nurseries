<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_devices extends Model
{
    use HasFactory;

    protected $fillable = ['device_id','notification_token'];
}
