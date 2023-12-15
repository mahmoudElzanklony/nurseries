<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_farm_address extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','address_id'];

    public function address(){
        return $this->belongsTo(user_addresses::class,'address_id');
    }
}
