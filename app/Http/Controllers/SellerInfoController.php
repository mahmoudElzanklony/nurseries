<?php

namespace App\Http\Controllers;

use App\Actions\ImageModalSave;
use App\Actions\SellerOrdersAndCustomOrdersAction;
use App\Enum\OrdersDeliveryCases;
use App\Http\Requests\SellerInfoFormRequest;
use App\Http\Resources\CountryResource;
use App\Http\traits\messages;
use App\Models\cities;
use App\Models\countries;
use App\Models\orders;
use App\Models\orders_shipment_info;
use App\Models\payments;
use App\Models\products;
use App\Models\users_bank_info;
use App\Models\users_commercial_info;
use App\Models\users_store_info;
use App\Services\sellers\StatisticsService;
use App\Services\statistics\Year_month_week_day;
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



    public function orders_money_products(){
        return messages::success_output('',StatisticsService::orders_money_products(auth()->id()));
    }

    public function profit_statistics(){
        $time_type = request('time_type');
        //return $data_model->get();
        $obj = new Year_month_week_day();
        $output = $obj->get_profit('App\Actions\SellerOrdersAndCustomOrdersAction',null,'money',$time_type,[],'orders.created_at');
        return $output;
    }

    public function clients_orders(){
        $time_type = request('time_type');
        //return $data_model->get();
        $obj = new Year_month_week_day();
        $output = $obj->get_profit('App\Actions\SellerOrdersClientsStatistics',null,'user_id',$time_type,[],'orders.created_at','count');
        return $output;
    }

     public function cities_statistics(){
        $users = countries::query()->whereHas('users',function($e){
            $e->whereHas('orders',function($e){
                $e->where('seller_id','=',auth()->id());
            });
        })->withcount('users')->selectRaw('id, '.app()->getLocale().'_name')->get();
        return CountryResource::collection($users);
        return "this api doesnt work because in ui based on cities and orders address based  geo location map so i think it will be best if its will be map ancor arrow (discussion)";
     }

}
