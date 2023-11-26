<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_packages extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','package_id','price','expiration_date'];

    public function package(){
        return $this->belongsTo(packages::class,'package_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
