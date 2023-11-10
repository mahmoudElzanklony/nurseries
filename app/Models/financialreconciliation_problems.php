<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class financialreconciliation_problems extends Model
{
    use HasFactory;

    protected $fillable = ['financial_reconciliation_id','orders','custom_orders','content'];
}
