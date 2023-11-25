<?php


namespace App\Http\traits\helpers_requests_api;


use App\Actions\CustomOrdersWithAllData;
use App\Actions\OrdersWithAllData;
use App\Filters\custom_orders\sellers\StatusFilter;
use App\Filters\EndDateFilter;
use App\Filters\orders\ClientNameFilter;
use App\Filters\orders\MaxPriceFilter;
use App\Filters\orders\MinPriceFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Http\Resources\CustomOrderResource;
use App\Http\Resources\OrderResource;
use App\Http\traits\messages;
use App\Models\custom_orders;
use App\Models\orders;
use App\Models\User;
use Illuminate\Pipeline\Pipeline;

trait OrdersHelperApi
{
    public function statistics_orders(){
        $all_orders = orders::query()->count() + custom_orders::query()->count();
        $cancelled = orders::query()->whereHas('last_shipment_info',function ($e){
            $e->where('content','=','cancelled');
        })->count() + custom_orders::query()->where('status','=','cancelled')->count();
        $pending = orders::query()->whereHas('last_shipment_info',function ($e){
                $e->where('content','!=','completed');
            })->count() + custom_orders::query()->where('status','!=','completed')->count();
        $completed = orders::query()->whereHas('last_shipment_info',function ($e){
                $e->where('content','=','completed');
            })->count() + custom_orders::query()->where('status','=','completed')->count();
        $output =  [
          'all_orders'=>$all_orders,
          'cancelled'=>$cancelled,
          'pending'=>$pending,
          'completed'=>$completed,
        ];
        return messages::success_output('',$output);
    }

    public function normal_orders_data(){
        $orders = OrdersWithAllData::get();
        $data = app(Pipeline::class)
            ->send($orders)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                StatusOrderFilter::class,
                ClientNameFilter::class
            ])
            ->thenReturn()
            ->orderBy('id','DESC')
            ->paginate(10);
        return OrderResource::collection($data);
    }

    public function custom_orders_data(){
        $orders = CustomOrdersWithAllData::get();
        $data = app(Pipeline::class)
            ->send($orders)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                \App\Filters\custom_orders\NameFilter::class,
                \App\Filters\marketer\StatusFilter::class
            ])
            ->thenReturn()
            ->paginate(10);
        return CustomOrderResource::collection($data);
    }
}
