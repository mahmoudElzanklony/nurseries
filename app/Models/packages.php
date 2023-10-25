<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class packages extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','price','type'];

    public function features(){
        return $this->hasMany(packages_features::class,'package_id');
    }

    public function payment(){
        return $this->morphOne(payments::class,'paymentable');
    }
}
