<?php


namespace App\Http\traits\helpers_requests_api;


use App\Actions\SendNotification;
use App\Filters\EndDateFilter;
use App\Filters\marketer\StatusFilter;
use App\Filters\marketer\UsernameFilter;
use App\Filters\orders\PaymentTypeFilter;
use App\Filters\StartDateFilter;
use App\Http\Resources\MarketerProfitResource;
use App\Http\traits\messages;
use App\Models\marketer_profit;
use App\Models\marketer_profit_percentage;
use App\Models\reports;
use Illuminate\Pipeline\Pipeline;

trait MarketerProfitHelperApi
{
    public function get_profit(){
       $data =  marketer_profit::query()->with('package_order',function($p){
            $p->with('package','user');
        })
            ->where('marketer_id','=',auth()->id())
            ->orderBy('id','DESC');
        $data = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                UsernameFilter::class,
                StatusFilter::class
            ])
            ->thenReturn()
            ->paginate();
        return MarketerProfitResource::collection($data);
    }

    public function profit_percentage(){
        return messages::success_output(marketer_profit_percentage::query()->first());
    }

    public function request_profit(){
        $profit_check = marketer_profit::query()
            ->find(request('id'));
        if($profit_check != null && $profit_check->marketer_id == auth()->id()){
            if($profit_check->status == 'ready_to_take'){
                $profit_check->status = 'ask_to_take';
                $profit_check->save();
                // make report
                $msg = trans('keywords.ask_to_take_profit').auth()->user()->username.trans('keywords.order_number').$profit_check->package_order_id;
                reports::query()->create([
                   'user_id'=>auth()->id(),
                   'type'=>'marketer_withdraw_pending',
                   'info'=>$msg
                ]);
                // send notification to admin
                SendNotification::to_admin(auth()->id(),$msg);
            }else{
                return messages::error_output(trans('errors.'.$profit_check->status.'_profit'));
            }
        }
    }
}
