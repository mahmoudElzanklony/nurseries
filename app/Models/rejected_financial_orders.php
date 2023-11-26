<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rejected_financial_orders extends Model
{
    use HasFactory;

    protected $fillable = ['financial_reconciliation_id','order_id','order_type'];
}
