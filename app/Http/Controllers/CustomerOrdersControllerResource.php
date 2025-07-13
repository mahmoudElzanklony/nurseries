<?php

namespace App\Http\Controllers;

use App\Actions\CustomOrdersWithAllData;
use App\Actions\DefaultAddress;
use App\Actions\GetHighDeliveryDays;
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
use App\Models\custom_orders_selected_products;
use App\Models\custom_orders_sellers;
use App\Models\custom_orders_sellers_reply;
use App\Models\custom_orders_shipment_info;
use App\Models\orders_shipment_info;
use App\Models\payments;
use App\Models\User;
use App\Models\user_addresses;
use App\Repositories\CustomOrdersRepository;
use CodeBugLab\NoonPayment\NoonPayment;
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
        $check->client_reply = 'rejected';
        $check->save();

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
        foreach($data['items'] as $datum){
            $datum['custom_orders_seller_id'] = $custom_order_to_seller->id;
            if(isset($datum['images'])) {
                $images = $datum['images'];
                unset($datum['images']);
            }
            $r = custom_orders_sellers_reply::query()->updateOrCreate([
                'id'=>$datum['id'] ?? null
            ],$datum);
            // upload images
            if(isset($images)){
                foreach($images as $image){
                    $img = $this->upload($image,'custom_orders_sellers_reply');
                    ImageModalSave::make($r->id,'custom_orders_sellers_reply','custom_orders_sellers_reply/'.$img);
                }
            }

        }
        // change status of seller to accepted
        $custom_order_to_seller->status = 'accepted';
        $custom_order_to_seller->save();

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
            $inputs_data = $request->validated();

            DB::beginTransaction();
            $data = custom_orders_sellers_reply::query()
                ->with('images')->with('custom_order_seller.order.images')
                ->where('custom_orders_seller_id','=',request('custom_orders_seller_id'))->get();
            if(sizeof($data) == 0){
                return messages::error_output(trans('errors.no_data'));
            }
            // it must be pending , active ==> this mean it in progress now
            //dd($data[0]->custom_order_seller->order);
            if($data[0]->custom_order_seller->order->status != 'pending'){
                return messages::error_output(trans('errors.cant_select_seller_to_this_order'));
            }

            // client select what he want
            $high_delivery_and_price = GetHighDeliveryDays::get($data);
            $noon_items_format = [];
            $total_money = 0;
            foreach($inputs_data['selected_items'] as $item){


                $selected_item = $data->first(function ($e) use ($item){
                    return $e->id == $item['id'];
                });
                if($selected_item == null){
                    return messages::error_output('هناك خطأ في تحديد المنتج الذي سيتم شرائة');
                }
                $price = $item['quantity'] * $selected_item->product_price;

                if($selected_item->quantity < $item['quantity']){
                    return messages::error_output('لقد قمت بطلب كمية من المنتج '.$selected_item->name.' وهي اكثر من المتوافر حاليا ');
                }
                $noon_item = [
                    'name'=>'custom order',
                    'quantity'=>$item['quantity'],
                    'unitPrice'=>$price,
                ];
                array_push($noon_items_format,$noon_item);
                custom_orders_selected_products::query()->create([
                    'custom_order_id'=>$data[0]->custom_order_seller->order->id,
                    'custom_orders_sellers_replies_id'=>$item['id'],
                    'quantity'=>$item['quantity'],
                    'price'=>$price,
                ]);
                $total_money += $price;
            }
            $total_money += $high_delivery_and_price['delivery_price'];

            // get all sellers and reject them
            $sellers_replies = custom_orders_sellers::query()
                ->where('custom_order_id','=',$data[0]->custom_order_seller->custom_order_id)
                ->where('id','!=',request('custom_orders_seller_id'))
                ->update(['client_reply'=>'rejected']);
            // handle visa payment
            $payment_status = $this->handle_payment($data[0]->custom_order_seller->order->id,$total_money);
            if($payment_status == true || request()->filled('payment')){
                // reject orders

                // accept only one
                custom_orders_sellers::query()->find($data[0]->custom_orders_seller_id)->update(['client_reply'=>'accepted']);


                // active order
                $this->make_order_active($data[0]->custom_order_seller->order->id);

                // send notification to accepted seller
                try{
                    $order_name = $data[0]->custom_order_seller->order->name;
                    $info_noti = [
                        'ar'=>'تم قبول عرضك المقدم بنجاح الخاص بطلب '.$order_name,
                        'en'=>'Your offer has been accepted successfully to order '.$order_name,
                    ];
                    SendNotification::to_any_one_else_admin
                    ($data[0]->custom_order_seller->seller_id,$info_noti,'/profile/custom-orders');
                }catch (\Throwable $e){
                    echo $e->getMessage();
                }
                /*$final = RepliesSellersWithAllData::get()
                    ->where('custom_order_id','=',$data->custom_order_seller->order->id)
                    ->where('seller_id','=',$data->custom_order_seller->seller_id)->first();*/
                $final = custom_orders_sellers::query()->with('order')->with(['seller','reply'=>function($e){
                    $e->with('images');
                }])->where('custom_order_id','=',$data[0]->custom_order_seller->order->id)
                    ->where('seller_id','=',$data[0]->custom_order_seller->seller_id)->first();

                $default_address = user_addresses::query()->
                    where('user_id','=',auth()->id())
                    ->where('default_address','=',1)
                    ->first();

                DB::commit();
                if(request('payment') == 'COD'){
                    $response = NoonPayment::getInstance()->initiate([
                        "order" => [
                            "reference" => $final->custom_order_id,
                            "amount" => $total_money,
                            "currency" => "SAR",
                            "name" => "Mraken Noon payment",
                            "items"=>$noon_items_format
                        ],
                        "billing"=> [
                            "address"=> [
                                "street"=> $default_address->default_address,
                                "city"=>"",
                                "stateProvince"=> "arabia sudia",
                                "country"=> "SA",
                                "postalCode"=> "12345"
                            ],
                            "contact"=> [
                                "firstName"=> auth()->user()->username,
                                "lastName"=> "",
                                "phone"=> auth()->user()->phone,
                                "mobilePhone"=> auth()->user()->phone,
                                "email"=> auth()->user()->email
                            ]
                        ],
                        "configuration" => [
                            "locale" => "ar"
                        ]
                    ]);



                    if ($response->resultCode == 0) {
                        return response()->json([
                            'url'=>$response->result->checkoutData->postUrl,
                            'total'=>$total_money,
                            'success_url'=>env('API_URL').'/noon_payment_response',
                            'failure_url'=>env('API_URL').'/noon_payment_response_failure',
                        ]);
                        return redirect($response->result->checkoutData->postUrl);
                    }
                }else{
                    return messages::success_output(trans('messages.saved_successfully')
                        ,CustomOrderSellerResource::make($final));
                }


                return $response;



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

    public function update_shipment_custom()
    {
        $custom = custom_orders::query()
                ->find(request('id'));
        if($custom != null){
            $custom->status = request('status');
            $custom->save();

            custom_orders_shipment_info::query()->updateOrCreate([
                'order_id'=>$custom->id,
                'type'=>'custom_order',
                'content'=>request('status'),
                'user_id'=>auth()->id()
            ],[
                'user_id'=>auth()->id(),
                'type'=>'custom_order',
                'content'=>request('status')
            ]);
        }else{
            return messages::error_output(trans('errors.not_found'));
        }

        $custom = CustomOrdersWithAllData::CustomOrderObj()->with('selected_products')->find($custom->id);
        return messages::success_output(trans('messages.saved_successfully'),CustomOrderResource::make($custom));
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

    public function handle_payment($order_id,$money){
        $visa_obj = new VisaPayment();
        PaymentModalSave::make($order_id,'custom_orders',$money);
        return true;
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
        if(auth()->user()->role->name == 'seller') {
            $data = CustomOrdersWithAllData::get()->with('order.selected_products.reply.images')->where('custom_order_id','=',$id)->first();
            $data['has_pending'] = true;

            return CustomOrderSellerResource::make($data);
        }else{
            $data = CustomOrdersWithAllData::get()->with('selected_products.reply.images')->find($id);
            $data['has_pending'] = true;
            return CustomOrderResource::make($data);
        }


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
