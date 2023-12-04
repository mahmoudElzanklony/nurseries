<?php


namespace App\Actions;


use App\Models\orders;
use App\Models\orders_items;
use App\Models\users_packages;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsStatisticsSalesAction
{
    public static function get($time_type = null){
        $data = DB::table('orders_items')
            ->join('products','orders_items.product_id','=','products.id')
            ->leftJoin('images','products.id','=','images.imageable_id')
            ->when($time_type != null , function ($e) use ($time_type){
                if($time_type != 'week') {
                    $fn = 'where' . ucfirst($time_type);
                    if ($time_type == 'year') {
                        $time = date('Y');
                    } else if ($time_type == 'day') {
                        $time = Carbon::today();
                        $fn = 'whereDate';
                    } else {
                        $time = date(($time_type == 'month' ? 'm' : 'Y-m-' . 'd'));
                    }
                    $e->$fn('orders_items.created_at', $time);
                }else{
                    $e->whereBetween('orders_items.created_at', [Carbon::now()->startOfWeek(Carbon::SATURDAY), Carbon::now()->endOfWeek(Carbon::FRIDAY)])->get();
                }
            })
            ->groupBy('product_id')
            ->selectRaw('sum(orders_items.quantity) as count_products ,products.id, products.'.app()->getLocale().'_name as name , images.name as image');
        return $data;
    }
}
