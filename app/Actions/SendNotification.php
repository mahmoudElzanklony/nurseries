<?php


namespace App\Actions;


use App\Models\notifications;

class SendNotification
{
    public static function to_admin($sender_id,$info,$url = ''){
        notifications::query()->create([
            'sender_id'=>$sender_id,
            'receiver_id'=>GetFirstAdmin::admin_info()->id,
            'content'=>$info,
            'url'=>$url,
            'seen'=>'0',
        ]);
    }

    public static function to_any_one_else_admin($receiver_id,$info,$url = ''){
        notifications::query()->create([
            'sender_id'=>GetFirstAdmin::admin_info()->id,
            'receiver_id'=>$receiver_id,
            'content'=>$info,
            'url'=>$url,
            'seen'=>'0',
        ]);
    }
}
