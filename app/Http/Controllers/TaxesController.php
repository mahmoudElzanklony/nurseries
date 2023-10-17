<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaxResource;
use App\Models\taxes;
use Illuminate\Http\Request;

class TaxesController extends Controller
{
    //
    public function index(){
        return TaxResource::make(taxes::query()->first());
    }
}
