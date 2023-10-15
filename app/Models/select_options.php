<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class select_options extends Model
{
    use HasFactory;

    protected $fillable = ['selectable_id','selectable_type','ar_name','en_name'];

    public function selectable(){
        return $this->morphTo();
    }
}
