<?php

namespace App\Http\Controllers;

use App\Actions\CustomOrdersWithAllData;
use App\Actions\DefaultAddress;
use App\Actions\ImageModalSave;
use App\Actions\PaymentModalSave;
use App\Actions\RepliesSellersWithAllData;
use App\Actions\SendNotification;
use App\Filters\custom_orders\SellerNameFilter;
use App\Filters\EndDateFilter;
use App\Filters\marketer\StatusFilter;
use App\Filters\NameFilter;
use App\Filters\StartDateFilter;
use App\Filters\TitleFilter;
use App\Http\Controllers\classes\payment\VisaPayment;
use App\Http\Requests\customOrderClientReplyFormRequest;
use App\Http\Requests\customOrderFormRequest;
use App\Http\Requests\sellerReplyCustomOrderFormRequest;
use App\Http\Resources\CustomOrderResource;
use App\Http\Resources\CustomOrderSellerReplyResource;
use App\Http\Resources\CustomOrderSellerResource;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\custom_orders;
use App\Models\custom_orders_sellers;
use App\Models\custom_orders_sellers_reply;
use App\Models\payments;
use App\Models\User;
use App\Repositories\CustomOrdersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\traits\upload_image;
use Illuminate\Pipeline\Pipeline;

class CustomerOrdersControllerResource extends Controller
{
    use upload_image;
    public function __construct(){
        return $this->middleware('CheckApiAuth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $data = CustomOrdersWithAllData::get();

        if(auth()->user()->role->name != 'seller') {
            $output = app(Pipeline::class)
                ->send($data)
                ->through([
                    \App\Filters\custom_orders\NameFilter::class,
                    StatusFilter::class,
                    SellerNameFilter::class,
                    StartDateFilter::class,
                    EndDateFilter::class,
                ])
                ->thenReturn()
                ->paginate(10);
            return CustomOrderResource::collection($output);
        }else{
            $output = app(Pipeline::class)
                ->send($data)
                ->through([
                    \App\Filters\custom_orders\NameFilter::class,
                    \App\Filters\custom_orders\sellers\StatusFilter::class,
                    SellerNameFilter::class,
                    \App\Filters\custom_orders\sellers\StartDateFilter::class,
                    \App\Filters\custom_orders\sellers\EndDateFilter::class
                ])
                ->thenReturn()
                ->paginate(10);
            return CustomOrderSellerResource::collection($output);
        }

    }
    public function reject_seller(){
        $check = custom_orders_sellers::query()
            ->where('custom_order_id','=',request('custom_order_id'))
            ->where('seller_id','=',auth()->id())->first();
        if($check == null){
            return messages::error_output(trans('errors.no_data'));
        }
        $check->update([
            'status'=>'rejected'
        ]);
        /*$output = custom_orders::query()
            ->with(['images','pending_alerts.reply.images',
                'accepted_alerts.reply.images','rejected_alerts.reply.images',
                'sellers_alerts'])->find(request('custom_order_id'));*/
        $output = custom_orders_sellers::query()->with(['order','reply','seller'])->find($check->id);
        return messages::success_output(trans('saved_successfully'),CustomOrderSellerResource::make($output));
    }
    public function reject(){
        $check = custom_orders_sellers::query()
            ->where('custom_order_id','=',request('custom_order_id'))
            ->where('seller_id','=',request('seller_id'))->first();
        if($check == null){
            return messages::error_output(trans('errors.no_data'));
        }
        $reply = custom_orders_sellers_reply::query()->where('custom_orders_seller_id','=',$check->id)->first();
        if($reply != null){
            $reply->client_reply = 'rejected';
            $reply->save();
        }
        return messages::success_output(trans('saved_successfully'),CustomOrderSellerResource::make($check));
    }

    public function seller_reply(sellerReplyCustomOrderFormRequest $request){
        DB::beginTransaction();
        $custom_order_to_seller = custom_orders_sellers::query()->with('order')
            ->where('seller_id','=',auth()->id())
            ->where('custom_order_id','=',request('custom_order_id'))->first();
        if($custom_order_to_seller == null){
            return messages::error_output(trans('errors.no_data'));
        }
        if($custom_order_to_seller->order->status != 'pending'){
            return messages::error_output(trans('errors.cant_reply_to_this_order'));
        }
        $data = $request->validated();
        $data['custom_orders_seller_id'] = $custom_order_to_seller->id;
        // change status of seller to accepted
        $custom_order_to_seller->status = 'accepted';
        $custom_order_to_seller->save();
        $images = [];
        if(request()->hasFile('images')){
            foreach(request()->file('images') as $img){
                $name = $this->upload($img,'custom_orders');
                array_push($images,$name);
            }
        }
        // save custom order
        $order = custom_orders_sellers_reply::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        // save images related to  order
        foreach($images as $image){
            ImageModalSave::make($order->id,'custom_orders_sellers_reply','custom_orders/'.$image);
        }

        $final_data = CustomOrdersWithAllData::get()->where('seller_id','=',auth()->id())
            ->where('custom_order_id','=',request('custom_order_id'))->first();
        DB::commit();
        return messages::success_output(trans('messages.saved_successfully'),CustomOrderSellerResource::make($final_data));
    }

    public function seller_requests(){
        // $data = CustomOrdersWithAllData::get()->whereHas()
    }

    public function client_reply(customOrderClientReplyFormRequest $request){

        if(request()->has('custom_orders_seller_id')){

            $data = custom_orders_sellers_reply::query()->with('images')->with('custom_order_seller.order.images')
                ->where('custom_orders_seller_id','=',request('custom_orders_seller_id'))->first();
            if($data == null){
                return messages::error_output(trans('errors.no_data'));
            }
            // it must be pending , active ==> this mean it in progress now
            if($data->custom_order_seller->order->status != 'pending'){
                return messages::error_output(trans('errors.cant_select_seller_to_this_order'));
            }
            // get all sellers and reject them
            $sellers_replies = custom_orders_sellers::query()->with('reply')
                ->where('custom_order_id','=',$data->custom_order_seller->custom_order_id)
                ->get();
            // handle visa payment
            $payment_status = $this->handle_payment(request('visa_id'),$data->custom_order_seller->order->id,$data->product_price + $data->delivery_price);
            if($payment_status == true){
                // reject orders
                foreach($sellers_replies as $sellers_reply){
                    try {
                        if ($sellers_reply->reply != null) {
                            custom_orders_sellers_reply::query()->find($sellers_reply->reply->id)->update([
                                'client_reply' => 'rejected'
                            ]);
                        }
                    }catch (\Throwable $e){
                        echo print_r($sellers_reply);
                    }
                }
                // accept only one
                $data->client_reply = 'accepted';
                $data->save();

                // active order
                $this->make_order_active($data->custom_order_seller->order->id);

                // send notification to accepted seller
                try{
                    $order_name = $data->custom_order_seller->order->name;
                    $info_noti = [
                        'ar'=>'تم قبول عرضك المقدم بنجاح الخاص بطلب '.$order_name,
                        'en'=>'Your offer has been accepted successfully to order '.$order_name,
                    ];
                    SendNotification::to_any_one_else_admin
                    ($data->custom_order_seller->seller_id,$info_noti,'/profile/custom-orders');
                }catch (\Throwable $e){
                    echo 'error.......';
                    echo $e->getMessage();
                }
                $final = RepliesSellersWithAllData::get()
                    ->where('custom_order_id','=',$data->custom_order_seller->order->id)
                    ->where('seller_id','=',$data->custom_order_seller->seller_id)->first();
                return messages::success_output(trans('messages.saved_successfully')
                    ,CustomOrderSellerResource::make($final));
            }else{
                return messages::error_output($payment_status);
            }



        }
    }


    public function send_request(){
        if(request()->has('sellers')) {
            foreach(request('sellers') as $seller) {
                $obj = custom_orders_sellers::query()->firstOrCreate([
                    'custom_order_id' => request('order_id'),
                    'seller_id' => $seller,
                ], [
                    'custom_order_id' => request('order_id'),
                    'seller_id' => $seller,
                ]);
            }
            $order = CustomOrdersWithAllData::get()->find(request('order_id'));
            $order->has_pending = true;
            return messages::success_output(trans('messages.saved_successfully'),CustomOrderResource::make($order));
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(customOrderFormRequest $request)
    {
        //
        $data = $request->validated();
        if(request()->has('id')){
            $order =custom_orders::query()->find(request('id'));
            $order->name = request('name');
            $order->save();
            return messages::success_output(trans('messages.saved_successfully'), CustomOrderResource::make(CustomOrdersWithAllData::get()->find($order->id)));
        }else {
            DB::beginTransaction();

            $default_address = DefaultAddress::get();
            // check if user has no default address for delivery order
            if ($default_address == null) {
                return messages::error_output(trans('errors.no_default_address'));
            }
            $images = [];
            if (request()->has('images')) {
                foreach (request('images') as $img) {
                    $name = $this->download_and_save($img, 'custom_orders');
                    array_push($images, $name);
                }
            }
            $custom_order = new CustomOrdersRepository($data['sellers'], $data);
            $custom_order->init_order($images)->send_alerts_to_sellers();
            DB::commit();
            return messages::success_output(trans('messages.order_done_successfully'), CustomOrderResource::make(CustomOrdersWithAllData::get()->find($custom_order->order->id)));
        }

    }

    public function handle_payment($visa_id,$order_id,$money){
        $visa_obj = new VisaPayment();
        if($visa_obj->handle(['id'=>$visa_id]) == true){
            PaymentModalSave::make($order_id,'custom_orders',$visa_id,$money);
            return true;
        }else{
            return false;
        }
    }

    public function make_order_active($order_id){
        custom_orders::query()->find($order_id)->update([
            'status'=>'active'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = CustomOrdersWithAllData::get()->find($id);
        $data['has_pending'] = true;
        return CustomOrderResource::make($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
