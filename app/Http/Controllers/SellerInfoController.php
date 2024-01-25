<?php

namespace App\Http\Controllers;

use App\Actions\ImageModalSave;
use App\Actions\SellerCustomOrdersClientsStatistics;
use App\Actions\SellerInfoWithAllData;
use App\Actions\SellerOrdersAndCustomOrdersAction;
use App\Enum\OrdersDeliveryCases;
use App\Http\Requests\SellerInfoFormRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserStoreInfoResource;
use App\Http\traits\messages;
use App\Models\cities;
use App\Models\countries;
use App\Models\orders;
use App\Models\orders_shipment_info;
use App\Models\payments;
use App\Models\products;
use App\Models\User;
use App\Models\user_addresses;
use App\Models\user_farm_address;
use App\Models\users_bank_info;
use App\Models\users_commercial_info;
use App\Models\users_store_info;
use App\Services\sellers\StatisticsService;
use App\Services\statistics\Year_month_week_day;
use Carbon\Carbon;
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
        return messages::success_output(trans('messages.saved_successfully'),UserStoreInfoResource::make($output));
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
        $output = $obj->get_profit('App\Actions\SellerOrdersClientsStatistics',null,'money',$time_type,[],'orders.created_at','sum');
        $output_two = $obj->get_profit('App\Actions\SellerCustomOrdersClientsStatistics',null,'money',$time_type,[],'custom_orders.created_at','sum');
        $final = [];
        foreach($output as $key => $item){
            $info = [];
            //dd($item['placeholder']);
            $info['placeholder'] = $item['placeholder'];

            $info['value'] = floatval($item['value']) + floatval($output_two[$key]['value']);
            array_push($final,$info);
        }

        return $final;
    }

     public function cities_statistics(){
        $countries = countries::query()->with('users',function($e){
            $e->with('orders',function($o){
                $o->groupBy('user_id');
            })->whereHas('orders',function($e){
                $e->where('seller_id','=',auth()->id());
            });
        })->get();
        $output = [];
        foreach ($countries as $key => $country){
            $output[] = [
                'id'=>$country->id,
                'name'=>$country->{app()->getLocale().'_name'},
                'users'=>sizeof($country->users)
            ];
        }
        return messages::success_output('',$output);
       // return "this api doesnt work because in ui based on cities and orders address based  geo location map so i think it will be best if its will be map ancor arrow (discussion)";
     }

     public function save_all_info(SellerInfoFormRequest $request){
         $data = $request->validated();

         users_store_info::query()->updateOrCreate([
             'user_id'=>auth()->id()
         ],$data['store_info']);
         $output = users_commercial_info::query()->updateOrCreate([
             'user_id'=>auth()->id()
         ],$data['commercial_info']);
         users_bank_info::query()->updateOrCreate([
             'user_id'=>auth()->id()
         ],$data['bank_info']);
         $data['location_info']['default_address'] = 1;
         user_farm_address::query()->updateOrCreate([
             'user_id'=>auth()->id()
         ],[
             'address_id'=>$data['location_info_id']
         ]);

         if(request()->hasFile('images')){
             foreach(request()->file('images') as $file){
                 $image = $this->upload($file,'sellers_commercial');
                 if($image){
                     ImageModalSave::make($output->id,'users_commercial_info','sellers_commercial/'.$image);
                 }
             }
         }
         $data = SellerInfoWithAllData::get()->find(auth()->id());
         return messages::success_output(trans('keywords.saved_successfully'),UserResource::make($data));
     }

     public function all_info(){
        $data = SellerInfoWithAllData::get()->find(auth()->id());
        return messages::success_output('',UserResource::make($data));
     }

}
