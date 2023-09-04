<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_bank_info extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','owner_name','bank_name','bank_account','bank_iban'];
}
