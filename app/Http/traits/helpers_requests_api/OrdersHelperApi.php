<?php


namespace App\Http\traits\helpers_requests_api;


use App\Actions\CustomOrdersWithAllData;
use App\Actions\ImageModalSave;
use App\Actions\OrdersWithAllData;
use App\Filters\custom_orders\sellers\StatusFilter;
use App\Filters\EndDateFilter;
use App\Filters\orders\ClientNameFilter;
use App\Filters\orders\MaxPriceFilter;
use App\Filters\orders\MinPriceFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Http\Requests\cancelOrderItemFormRequest;
use App\Http\Resources\CancelOrderItemResource;
use App\Http\Resources\CustomOrderResource;
use App\Http\Resources\OrderResource;
use App\Http\traits\messages;
use App\Models\cancelled_orders_items;
use App\Models\custom_orders;
use App\Models\financial_reconciliations;
use App\Models\orders;
use App\Models\orders_items;
use App\Models\User;
use App\Services\mail\send_email;
use Illuminate\Pipeline\Pipeline;
use App\Http\traits\upload_image;
trait OrdersHelperApi
{
    use upload_image;
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

    public function cancel_item(cancelOrderItemFormRequest $request){
        $data = $request->validated();
        $order_item = orders_items::query()->findOrFail(request('order_item_id'));
        $order = orders::query()->with('seller')->find($order_item->order_id);
        if($order->financial_reconciliation_id != null){
            $financial = financial_reconciliations::query()->find($order->financial_reconciliation_id);
            if($financial->status != 'completed'){
                return messages::error_output('لقد تم اتخاذ اجراء مع هذا الطلب التابع لاوردر رقم '.$order->id.' لذلك لا تستطيع اتخاذ اي اجراء عليه الان ');
            }
        }
        $item = cancelled_orders_items::query()->firstOrCreate([
            'order_item_id'=>$data['order_item_id']
        ],$data);
        // send email to seller
        send_email::send('الغاء طلب','سيتم الغاء رقم القطعه '.$order_item->id.' التابعه لطلب رقم '.$order->id.'وذلك بسبب رساله من الاداره محتواها '.$data['content'],
            '','اضغط هنا',$order->seller->email);
        $item = cancelled_orders_items::query()->with('order_item')->find($item->id);
        if(request()->hasFile('images')){
            foreach(request()->file('images') as $img){
                $image = $this->upload($img,'cancelled_orders');
                ImageModalSave::make($item->id,'cancelled_orders_items','cancelled_orders/'.$image);
            }
        }
        return messages::success_output(trans('messages.saved_successfully'),CancelOrderItemResource::make($item));
    }
}
