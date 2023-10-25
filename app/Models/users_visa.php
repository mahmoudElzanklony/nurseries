<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class users_visa extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users_visa';

    protected $fillable = ['user_id','name','card_number','end_date','cvv'];

    public function orders(){
        return $this->hasMany(payments::class,'visa_id')
            ->where('paymentable_type','=','App\Models\orders');
    }

    public function custom_orders(){
        return $this->hasMany(payments::class,'visa_id')
            ->where('paymentable_type','=','App\Models\custom_orders');
    }

    public function packages(){
        return $this->hasMany(payments::class,'visa_id')
            ->where('paymentable_type','=','App\Models\users_packages');
    }
}
