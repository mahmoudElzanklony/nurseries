<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use App\Models\ads;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    //
    public function index()
    {
        $data = ads::query()->orderBy('order','ASC')->get();
        return AdResource::collection($data);
    }
}
