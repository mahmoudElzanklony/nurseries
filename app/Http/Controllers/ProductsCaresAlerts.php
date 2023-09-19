<?php

namespace App\Http\Controllers;

use App\Http\Requests\productsCareFormRequest;
use App\Http\Resources\UsersProductsCareAlertsResource;
use App\Http\traits\messages;
use App\Models\users_products_care_alerts;
use App\Models\users_products_cares;
use Illuminate\Http\Request;

class ProductsCaresAlerts extends Controller
{
    //

    public function get_alerts_for_this_user($product_id){
        $alerts = users_products_care_alerts::query()->with('product_care')
            ->where('user_id','=',auth()->id())
            ->where('product_id','=',$product_id)->get();
        return UsersProductsCareAlertsResource::collection($alerts);
    }



}
