<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class user_addresses extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id','longitude','latitude','address','default_address'];

    public function area(){
        return $this->belongsTo(areas::class,'area_id');
    }
}
