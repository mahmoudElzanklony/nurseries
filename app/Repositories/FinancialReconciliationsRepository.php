<?php


namespace App\Repositories;


use App\Actions\DetectRole;
use App\Models\orders;
use App\Models\User;

class FinancialReconciliationsRepository
{


    public function store_data($orders_ids){
        $orders = orders::query()->with('items')
            ->whereIn('ids',$orders_ids);
        if(DetectRole::get() == 'seller'){
            $orders = $orders->where('seller_id','=',auth()->id());
        }

        foreach($orders->get() as $order){
            $total_money = $order->items->sum('price');
            dd($total_money);
        }

    }

}
