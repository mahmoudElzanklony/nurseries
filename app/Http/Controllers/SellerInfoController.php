<?php

namespace App\Http\Controllers;

use App\Actions\ImageModalSave;
use App\Enum\OrdersDeliveryCases;
use App\Http\Requests\SellerInfoFormRequest;
use App\Http\traits\messages;
use App\Models\orders;
use App\Models\orders_shipment_info;
use App\Models\products;
use App\Models\users_bank_info;
use App\Models\users_commercial_info;
use App\Models\users_store_info;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
class SellerInfoController extends Controller
{
    //
    use upload_image;

    public function save_store(SellerInfoFormRequest $request){
        $data = $request->validated();
        $output = users_store_info::query()->updateOrCreate([
            'user_id'=>auth()->id()
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),$output);
    }

    public function save_commercial_infos(SellerInfoFormRequest $request){
        $data = $request->validated();
        $output = users_commercial_info::query()->updateOrCreate([
            'user_id'=>auth()->id()
        ],$data);
        if(request()->hasFile('images')){
            foreach(request()->file('images') as $file){
                $image = $this->upload($file,'sellers_commercial');
                if($image){
                    ImageModalSave::make($output->id,'users_commercial_info','sellers_commercial/'.$image);
                }
            }
        }
        return messages::success_output(trans('messages.saved_successfully'),$output);
    }

    public function save_bank(SellerInfoFormRequest $request){
        $data = $request->validated();
        $output = users_bank_info::query()->updateOrCreate([
            'user_id'=>auth()->id()
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),$output);
    }

    public function my_orders(){
        return $my_orders = orders::query()->whereHas('seller',function($e){
            $e->where('seller_id','=',auth()->id());
        });
    }

    public function orders_money_products(){

        $output = [
          'active_orders'=>$this->my_orders()->wherehas('shipments_info',function($e){
              $e->where('content','=',OrdersDeliveryCases::$delivery);
          })->count(),
          'waiting_orders'=>$this->my_orders()->wherehas('shipments_info',function($e){
              $e->where('content','!=',OrdersDeliveryCases::$delivery);
          })->count(),
          'pending_money'=>$this->my_orders()->whereRaw('financial_reconciliation_id is null')->withSum('payment','money')->get()->sum('payment_sum_money'),
          'active_money'=>$this->my_orders()->whereRaw('financial_reconciliation_id is not null')->withSum('payment','money')->get()->sum('payment_sum_money'),
          'products'=>products::query()->where('user_id','=',auth()->id())->count(),
          'my_clients'=>$this->my_orders()->groupBy('user_id')->get()->count()
        ];
        return messages::success_output('',$output);
    }

}
