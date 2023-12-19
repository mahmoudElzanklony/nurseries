<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class notifications_jobs extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['name','notification_template_id','notification_type','user_type','send_at','content','status'];

    public function template()
    {
        return $this->belongsTo(notifications_templates::class,'notification_template_id');
    }
}
