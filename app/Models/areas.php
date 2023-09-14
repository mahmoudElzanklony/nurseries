<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class areas extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['city_id','ar_name','en_name'];

    public function city(){
        return $this->belongsTo(cities::class,'city_id');
    }
}
