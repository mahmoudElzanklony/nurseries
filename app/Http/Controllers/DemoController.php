<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemoController extends Controller
{
    //
    public function demo()
    {
        if(request()->has('email') && request()->has('password')){
            $result = [
              "message"=>"welcome to ".request('email'),
              "status"=>200
            ];
            return response()->json($result);
        }else{
            $result = [
                "message"=>"error you must send email and password",
                "status"=>400
            ];
            return response()->json($result,400);
        }
    }
}
