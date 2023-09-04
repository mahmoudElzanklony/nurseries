<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_commercial_info extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','commercial_register','tax_card'];
}
