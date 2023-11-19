<?php

namespace App\Http\Controllers;

use App\Actions\ProductWithAllData;
use App\Filters\CategoryIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\IDsFilter;
use App\Filters\NameFilter;
use App\Filters\products\MaxPriceFilter;
use App\Filters\products\MinPriceFilter;
use App\Filters\products\SellerNameFilter;
use App\Filters\StartDateFilter;
use App\Filters\UserIdFilter;
use App\Http\Resources\ProductResource;
use App\Models\orders_items;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class BestController extends Controller
{
    //
    public function rates(){
        $products_ids = orders_items::query()
            ->join('orders_items_rates','orders_items_rates.order_item_id','=','orders_items.id')
            ->selectRaw('orders_items.product_id , avg(orders_items_rates.rate_product_info) as avg_rate')
            ->groupBy('orders_items.product_id')
            ->having('avg_rate','>=',4)
            ->get()->map(function($e){
                return $e['product_id'];
            });
        $output = ProductWithAllData::get()->whereIn('id',$products_ids)->paginate(10);
        $output =  ProductResource::collection($output);
        return $output;
    }

    public function orders(){
        $data = ProductWithAllData::get()->has('orders_items')
                ->withCount('orders_items')
                ->orderBy('orders_items_count','DESC')
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                CategoryIdFilter::class,
                MinPriceFilter::class,
                MaxPriceFilter::class,
                UserIdFilter::class,
                IDsFilter::class,
                NameFilter::class,
            ])
            ->thenReturn()
            ->paginate(10);
        return ProductResource::collection($data);
    }
}
