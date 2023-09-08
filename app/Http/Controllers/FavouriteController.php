<?php

namespace App\Http\Controllers;

use App\Actions\ProductWithAllData;
use App\Http\Resources\ProductResource;
use App\Http\traits\messages;
use App\Models\favourites;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    //
    public function index(){
        $fav = favourites::query()->where('user_id','=',auth()->id())
            ->orderBy('id','DESC')->paginate(15);
        $ids = $fav->getCollection()->map(function ($e) {
            return $e->product_id;
        });
        $output = ProductWithAllData::get()->whereIn('id', $ids)->paginate(15);
        return ProductResource::collection($output);
    }
}
