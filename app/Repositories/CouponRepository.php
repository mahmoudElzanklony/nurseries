<?php


namespace App\Repositories;


use App\Http\Resources\CouponRessource;
use App\Http\traits\messages;
use App\Models\coupons;
use App\Models\coupons_products;
use App\Models\users_coupons;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CouponRepository
{
    public $coupon;
    public $error = '';
    public function init($data){
        DB::beginTransaction();
        $data['user_id'] = auth()->id();
        $this->coupon = coupons::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        return $this;
    }

    public function coupons_products($products){
        if(sizeof($products) > 0) {
            $products = collect($products)->map(function($e){
               return ['product_id'=>$e,'created_at'=>now()];
            });
          //  $products = array_merge(...$products);

            $this->coupon->products()->sync($products);
        }
        DB::commit();
        return messages::success_output(trans('messages.saved_successfully'),CouponRessource::make($this->coupon));
    }

    public function validate_exist($code , $products = []){
        $coupon = coupons::query()->where('code','=',$code)
            ->first();
        if($coupon != null){
            // check date
            if(!($coupon->end_date == null || $coupon->end_date > Carbon::now())){
                $this->error = trans('errors.expired_coupon_date');
            }else if($coupon == 0){
                $this->error = trans('errors.coupon_amount_end');
            }else {
                $this->coupon = $coupon;

                return $this->is_used_by_user(auth()->id(),$products);
            }
        }
    }

    public function is_used_by_user($user_id = null , $products){
        if($user_id == null){
            $user_id = auth()->id();
        }
        if(isset($this->coupon)){
            $user_used = users_coupons::query()
                ->where('coupon_id','=',$this->coupon->id)
                ->where('user_id','=',$user_id)->first();
            if($user_used == null){
                // check if this product support these products or not
                if(sizeof($products) > 0){
                    $products_check = coupons_products::query()
                        ->where('coupon_id','=',$this->coupon->id)
                        ->whereIn('product_id',$products)->get();
                    if(sizeof($products_check) == 0){
                        // this coupon doesnt support these products
                        return $this->error = trans('errors.coupon_doesnt_support_products');
                    }else{
                        return $this->coupon;
                    }
                }else {
                    return $this->coupon;
                }
            }
        }
        $this->error = trans('errors.coupon_used_before');
    }
}
