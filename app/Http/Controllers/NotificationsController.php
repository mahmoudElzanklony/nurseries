<?php

namespace App\Http\Controllers;

use App\Actions\ShowNotifications;
use App\Http\Resources\NotificationResource;
use App\Http\traits\messages;
use App\Models\notifications;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //
    // all notifications
    public function index(){
        return NotificationResource::collection(ShowNotifications::get_data());

    }

    public function statistics(){
        $data = notifications::query()->where('receiver_id','=',auth()->id());
        $output = [
          'all'=>$data->count(),
          'unseen'=>$data->where('seen','=',0)->count()
        ];
        notifications::query()->where('receiver_id','=',auth()->id())->update([
            'seen'=>0
        ]);
        return messages::success_output('',$output);
    }
}
