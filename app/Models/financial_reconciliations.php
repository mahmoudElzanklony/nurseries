<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class financial_reconciliations extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','seller_id','total_money','admin_profit_percentage','status'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function image(){
        return $this->morphOne(images::class,'imageable');
    }

    public function orders(){
        return $this->hasMany(orders::class,'financial_reconciliation_id');
    }

    public function custom_orders(){
        return $this->hasMany(custom_orders::class,'financial_reconciliation_id');
    }

    public function problem(){
        return $this->hasOne(financialreconciliation_problems::class,'financial_reconciliation_id');
    }

}
