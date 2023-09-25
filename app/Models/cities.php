<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class cities extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['country_id','ar_name','en_name','map_code'];

    public function country(){
        return $this->belongsTo(countries::class,'country_id');
    }


}
