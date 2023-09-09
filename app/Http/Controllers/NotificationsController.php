<?php

namespace App\Http\Controllers;

use App\Actions\ShowNotifications;
use App\Http\Resources\NotificationResource;
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
}
