<?php


namespace App\Actions;


use App\Models\financial_reconciliations;
use App\Models\orders;
use App\Models\User;

class FinancialRecociliationsWithAllData
{

    public static function get_data(){
        $user = User::query()->with('role')->find(auth()->id());
        $data = financial_reconciliations::query()
            ->with(['user','image']);

        if($user->role->name == 'admin'){
            $data->with('orders');
        }else{
            $data->with('orders')->whereHas('orders',function ($e){
               $e->where('seller_id','=',auth()->id());
            });
        }
        return $data->orderBy('id','DESC');

    }



}
