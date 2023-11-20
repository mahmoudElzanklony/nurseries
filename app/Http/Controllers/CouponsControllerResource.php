<?php

namespace App\Http\Controllers;

use App\Http\Requests\couponsFormRequest;
use App\Http\Resources\CouponRessource;
use App\Http\traits\messages;
use App\Models\coupons;
use App\Models\users_coupons;
use App\Repositories\CouponRepository;
use Illuminate\Http\Request;

class CouponsControllerResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('CheckApiAuth');
    }
    public function index()
    {
        $data = coupons::query()->with('products')
            ->where('user_id','=',auth()->id())
            ->orderBy('id','DESC')
            ->withCount('users')
            ->get();
        return CouponRessource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(couponsFormRequest $request)
    {
        //
        $data = $request->validated();
        $coupon_repoj = new CouponRepository();
        return $coupon_repoj->init($data)->coupons_products(request('products') ?? []);
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
        $coupon = coupons::query()->with('products')
            ->with('users.user')->with('order_items',function($e){
            $e->with('product')->selectRaw('*,count(product_id) as products_count')->groupBy('product_id');
        })->where('user_id','=',auth()->id())->find($id);
        return CouponRessource::make($coupon);
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
        $coupon = coupons::query()->find($id);
        $coupon->status = request('status');
        $coupon->save();
        return messages::success_output(trans('messages.saved_successfully'),CouponRessource::make($coupon));
    }

    public function validate_coupon(){
        $coupon_repos = new CouponRepository();
        $coupon = $coupon_repos->validate_exist(request('code'));
        if($coupon_repos->error != ''){
            return messages::error_output($coupon_repos->error);
        }else {
            return messages::success_output('',CouponRessource::make($coupon_repos->coupon));
        }
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
