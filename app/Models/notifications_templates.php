<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications_templates extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','notification_type_id','user_type','content'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function notification_type(){
        return $this->belongsTo(notifications_types::class,'notification_type_id');
    }
}
