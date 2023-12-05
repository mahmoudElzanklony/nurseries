<?php

namespace App\Http\Controllers;

use App\Actions\ProductWithAllData;
use App\Http\Resources\ProductResource;
use App\Services\SearchesResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchesController extends Controller
{
    //
    public function products(){
        $data = SearchesResults::last_searches('products');

        // get id only from searches
        $ids =  $data->getCollection()->map(function($e){
            return $e->item_id;
        })->toArray();

        $ids_ordered = implode(',', $ids);

        $final_data = ProductWithAllData::get()
            ->whereIn('id',$ids)->orderByRaw(DB::raw("FIELD(id, $ids_ordered)"))->paginate(15);
        return ProductResource::collection($final_data);
    }
}
