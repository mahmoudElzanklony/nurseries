<?php


namespace App\Http\traits\helpers_requests_api;


use App\Actions\MyCurrentPoints;
use App\Http\Resources\MarketerClientResource;
use App\Http\traits\messages;
use App\Models\followers;
use App\Models\marketer_clients;
use App\Models\orders;
use App\Models\packages_orders;
use App\Models\products;
use App\Models\projects;
use App\Models\User;
use App\Services\DB_connections;
use Carbon\Carbon;

trait QuickReportUserHelperApi
{
    use messages;
    public function quick_report($id = null){
        if($id == null){
            if(request()->has('id')){
                $id = request('id');
            }else{
                $id = auth()->id();
            }
        }
        $user = User::query()->where('id','=',auth()->id())->with('role')->first();
        if($user->role->name == 'client'){
            return messages::success_output('',$this->client_report($id));
        }else if($user->role->name == 'seller'){
            return messages::success_output('',$this->seller_report($id));
        }
    }

    public function client_report(){

        $result = [
            'orders'=>orders::query()->where('user_id','=',auth()->id())->count(),
            'following'=>followers::query()->where('user_id','=',auth()->id())->count(),
        ];
        return $result;

    }

    public function seller_report($id){
        $result = [
            'products'=>products::query()->where('user_id','=',$id)->count(),
            'followers'=>followers::query()->where('following_id',$id)->count(),
            'orders'=>orders::query()->where('seller_id','=',$id)->count(),
        ];
        return $result;
    }

    public function handle_date($result,$data,$client = null){

        $transactions_number = 0;  $branches_count = 0;  $operations_count = 0;
        foreach($data as $d){
            $branches_count += $d->branches_count;
            $operations_count += $d->operations_count;
            foreach($d['operations'] as $operation){
                $transactions_number+= $operation->transactions_count;
            }
        }
        $result['projects'] = sizeof($data);
        $result['branches'] = $branches_count;
        $result['operations'] = $operations_count;
        $result['transactions'] = $transactions_number;
        if($client != null){
            // marketer process to get client info
            $result['client'] = MarketerClientResource::make($client);
        }
        return $result;

    }
}
