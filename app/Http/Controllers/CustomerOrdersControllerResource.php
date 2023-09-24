<?php

namespace App\Http\Controllers;

use App\Actions\DefaultAddress;
use App\Http\Requests\customOrderFormRequest;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\custom_orders;
use App\Models\User;
use App\Repositories\CustomOrdersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrdersControllerResource extends Controller
{
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
