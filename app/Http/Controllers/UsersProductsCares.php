<?php

namespace App\Http\Controllers;

use App\Actions\ManageTimeAlert;
use App\Actions\ProductWithAllData;
use App\Http\Requests\productsCareFormRequest;
use App\Http\Resources\CareResource;
use App\Http\Resources\ProductCareResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UsersProductsCareResource;
use App\Http\traits\messages;
use App\Models\care;
use App\Models\products;
use App\Models\products_care;
use App\Models\users_products_care_alerts;
use App\Models\users_products_cares;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersProductsCares extends Controller
{
    //

    public function get_products_cares(){
        $data =  ProductWithAllData::get()->whereHas('user_care',function($e){
            $e->where('user_id','=',auth()->id());
        })->paginate(10);
            /*->whereHas('cares',function ($e){
                $e->whereRaw('(type = "seller" OR user_id = '.auth()->id().')');
            })*/
           // ->where('user_id','=',auth()->id())->get();
            /*foreach($data as $datum){
                $datum['cares'] = products_care::query()->with('care')
                    ->where('product_id','=',$datum->product_id)
                    ->whereRaw('(user_id = '.auth()->id().' OR type = "seller")')->get();
            }*/
        return ProductResource::collection($data);
    }

    public function find($id){
        $data =  ProductWithAllData::get()->whereHas('user_care',function($e){
            $e->where('user_id','=',auth()->id());
        })->paginate(10);
        return ProductResource::collection($data);
    }

    public function check_before_add_product_to_care($product_id){
        return users_products_cares::query()
            ->where('user_id','=',auth()->id())
            ->where('product_id','=',$product_id)->first();
    }

    public function send_care_alerts($product_id){
        $product_cares = products_care::query()
            ->where('product_id','=',$product_id)
            ->where('type','=','seller')
            ->get();
        foreach($product_cares as $care){
            users_products_care_alerts::query()->updateOrCreate([
                'product_care_id'=>$care->id,
                'user_id'=>auth()->id(),
            ],[
                'product_care_id'=>$care->id,
                'user_id'=>auth()->id(),
                'next_alert'=>ManageTimeAlert::manage($care->time_number,$care->time_type,null)
            ]);
        }
    }

    public function has_care_check($product_id){
        return products_care::query()->where('product_id','=',$product_id)->first();
    }

    public function add(){
        DB::beginTransaction();
        $check_before_add = $this->check_before_add_product_to_care(request('product_id'));
        if($check_before_add == null){
            // check this product has care
            if($this->has_care_check(request('product_id')) == null){
                return messages::error_output('هذا المنتج لا يحتوي علي خصائص الرعاية');
            }
            // add product to care list
            $output = users_products_cares::query()->create([
               'product_id'=>request('product_id'),
               'user_id' =>auth()->id()
            ]);
            // make  alerts for every product care to this user
            $this->send_care_alerts(request('product_id'));
            DB::commit();
            return messages::success_output(trans('messages.saved_successfully'),ProductResource::make(ProductWithAllData::get()->find($output->product_id)));
        }else{
            return messages::error_output(trans('errors.item_exist_at_care_list'));
        }

    }

    public function ability_to_make_custom_care($product_id,$care_id){
        $seller_check = products_care::query()
            ->where('product_id','=',$product_id)
            ->where('care_id','=',$care_id)
            ->where('type','=','seller')
            ->first();
        if($seller_check != null){
            return false;
        }else{
            // seller not add it
            $client_check = products_care::query()
                ->where('product_id','=',$product_id)
                ->where('care_id','=',$care_id)
                ->where('user_id','=',auth()->id())
                ->first();
            if($client_check != null){
                return false;
            }else{
                return true;
            }
        }
    }

    public function make_custom_product_care(productsCareFormRequest $request){
        $data = $request->validated();
        $data['type'] = 'client';
        $data['user_id'] = auth()->id();
        $check = $this->ability_to_make_custom_care($data['product_id'],$data['care_id']);
        if($check == true){
            // check this products already added to list care
            $product_care_to_user_check = users_products_cares::query()
                ->where('user_id','=',auth()->id())
                ->where('product_id','=',$data['product_id'])->first();
            if($product_care_to_user_check == null){
                return messages::error_output(trans('errors.item_doesnt_exist_at_care_list'));
            }
            $care_obj = products_care::query()->with(['care','next_time'=>function($e){
                $e->where('user_id','=',auth()->id());
            }])->updateOrCreate([
                'id'=>request('id') ?? null
            ],$data);
            // make alert for this care
            users_products_care_alerts::query()->updateOrCreate([
                'user_id'=>auth()->id(),
                'product_care_id'=>$care_obj->id,
            ],[
                'user_id'=>auth()->id(),
                'product_care_id'=>$care_obj->id,
                'next_alert'=>request('next_alert') ?? ManageTimeAlert::manage($data['time_number'],$data['time_type'],null)
            ]);
            $output = ProductCareResource::make($care_obj);
            return messages::success_output(trans('messages.saved_successfully'),$output);
        }else{
            return messages::error_output('لا تستيطع اضافة عملية رعاية خاصه لهذا المنتج');
        }
    }

    public function questions(){
        $product_id = request('product_id');
        $item_check = users_products_cares::query()
            ->where('user_id','=',auth()->id())
            ->where('product_id','=',$product_id)->first();
        if($item_check == null){
            return messages::error_output(trans('errors.item_doesnt_exist_at_care_list'));
        }
        // get product cares added from seller or by user custom
        $cares = products_care::query()
            ->where('product_id','=',$product_id)
            ->whereRaw('(user_id = '.auth()->id().' OR type = "seller")')->select('care_id')
            ->get();
        if(sizeof($cares) > 0){
            $cares = $cares->map(function ($e){
                return $e->care_id ;
            });
        }
        $data = care::query()->whereNotIn('id',$cares)->get();

        return CareResource::collection($data);
    }

}
