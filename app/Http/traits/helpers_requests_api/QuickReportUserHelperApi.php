<?php


namespace App\Http\traits\helpers_requests_api;


use App\Actions\MyCurrentPoints;
use App\Http\Resources\MarketerClientResource;
use App\Http\traits\messages;
use App\Models\marketer_clients;
use App\Models\packages_orders;
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
        }else if($user->role->name == 'marketer'){
            return messages::success_output('',$this->markter_report($id));
        }
    }

    public function markter_report(){
        $result = [];
        $clients_of_markter = marketer_clients::query()->with('client')
            ->select('client_id')
            ->where('marketer_id','=',auth()->id())->get();
        $output = [];
        foreach($clients_of_markter as $client){


            DB_connections::get_wanted_tenant_user($client->client_id);
            $data = projects::query()->select('id')
                ->with('operations',function($q){
                    $q->select('branch_id')->withCount('transactions');
                })
                ->withCount('branches','operations')
                ->where('user_id','=',$client->client_id)->get();
            $output[] =  $this->handle_date($result,$data,$client->client);
        }
        return $output;

    }

    public function client_report($id){
        $result = [
            'points'=>MyCurrentPoints::get(),
            'wallet'=>auth()->user()->wallet,
        ];
        DB_connections::get_wanted_tenant_user();
        $data = projects::query()->select('id')
            ->with('operations',function($q){
                $q->select('branch_id')->withCount('transactions');
            })
            ->withCount('branches','operations')
            ->where('user_id','=',$id ?? auth()->id())->get();

        return $this->handle_date($result,$data);
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
