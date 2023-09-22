<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class care extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['ar_name','en_name','is_required'];

    public function products_care(){
        return $this->hasMany(products_care::class,'care_id');
    }
}
