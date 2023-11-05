<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class financial_reconciliations_profit_percentages extends Model
{
    use HasFactory;

    protected $fillable = ['from_who','percentage','note'];

    protected $table = 'financial_reconciliations_proit_percentages';
}
