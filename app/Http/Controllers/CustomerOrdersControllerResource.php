<?php

namespace App\Http\Controllers;

use App\Actions\DefaultAddress;
use App\Actions\ImageModalSave;
use App\Http\Requests\customOrderFormRequest;
use App\Http\Requests\sellerReplyCustomOrderFormRequest;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\custom_orders;
use App\Models\custom_orders_sellers;
use App\Models\custom_orders_sellers_reply;
use App\Models\User;
use App\Repositories\CustomOrdersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\traits\upload_image;
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
        $data = custom_orders::query()->with(['images','sellers_alerts'=>function($e){
            $e->with(['seller','reply'=>function($e){
                $e->with('images');
            }]);
        }])->orderBy('id','DESC')->paginate(10);
        return $data;
    }

    public function seller_reply(sellerReplyCustomOrderFormRequest $request){
        DB::beginTransaction();
        $custom_order_to_seller = custom_orders_sellers::query()
            ->where('seller_id','=',auth()->id())
            ->where('custom_order_id','=',request('custom_order_id'))->first();
        if($custom_order_to_seller == null){
            return messages::error_output(trans('errors.no_data'));
        }
        $data = $request->validated();
        $data['custom_orders_seller_id'] = $custom_order_to_seller->id;
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
            ImageModalSave::make($order->id,'custom_orders','custom_orders/'.$image);
        }
        DB::commit();
        return messages::success_output(trans('saved_successfully'),$order);
    }

    public function client_reply(){
        if(request()->has('custom_orders_seller_id')){
            $data = custom_orders_sellers_reply::query()->with('custom_order_seller')
                ->where('custom_orders_seller_id','=',request('custom_orders_seller_id'))->first();
            if($data == null){
                return messages::error_output(trans('errors.no_data'));
            }
            // get all sellers and reject them
            $sellers_replies = custom_orders_sellers::query()->with('reply')
                ->where('custom_order_id','=',$data->custom_order_seller->custom_order_id)
                ->get();
            foreach($sellers_replies as $sellers_reply){
                custom_orders_sellers_reply::query()->find( $sellers_reply->reply->id)->update([
                    'client_reply' => 'rejected'
                ]);
            }
            // accept only one
            $data->client_reply = 'accepted';
            $data->save();
            return messages::success_output(trans('messages.saved_successfully'));
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
        DB::beginTransaction();
        $data = $request->validated();
        $default_address = DefaultAddress::get();
        // check if user has no default address for delivery order
        if($default_address == null){
            return messages::error_output(trans('errors.no_default_address'));
        }
        $images = [];
        if(request()->hasFile('images')){
            foreach(request()->file('images') as $img){
                $name = $this->upload($img,'custom_orders');
                array_push($images,$name);
            }
        }
        $custom_order = new CustomOrdersRepository($data['sellers'],$data);
        $custom_order->init_order($images)->send_alerts_to_sellers();
        DB::commit();
        return messages::success_output(trans('messages.order_done_successfully'),$custom_order->order);

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
        dd('show id');
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
