<?php

namespace App\Http\Controllers;

use App\Actions\FinancialRecociliationsWithAllData;
use App\Filters\EndDateFilter;

use App\Filters\financial\SellerId;
use App\Filters\marketer\StatusFilter;
use App\Filters\StartDateFilter;

use App\Filters\UserIdFilter;
use App\Http\Requests\financialReconciliationFormRequest;
use App\Http\Resources\FinancialReconciliationResource;
use App\Http\traits\messages;
use App\Models\financial_reconciliations;
use App\Repositories\FinancialReconciliationsRepository;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class FinancialReconciliationsControllerResource extends Controller
{

    public function __construct(){
        $this->middleware('CheckApiAuth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = FinancialRecociliationsWithAllData::get_data();

        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                UserIdFilter::class,
                SellerId::class,
                StatusFilter::class
            ])
            ->thenReturn()
            ->paginate(10);
        return $output;
        return FinancialReconciliationResource::collection($output);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $financil_repo = new FinancialReconciliationsRepository();
        $orders = $financil_repo->get_orders_to_be_financial(false);
        if(sizeof($orders['orders']) > 0 || sizeof($orders['custom_orders']) > 0){
            $financil_repo->store_data($orders['orders'],$orders['custom_orders']);
            return messages::success_output(trans('messages.saved_successfully'));
        }


    }

    public function statistics(){
        $financil_repo = new FinancialReconciliationsRepository();
        $orders = $financil_repo->get_orders_to_be_financial(false);
        $pending_money = $financil_repo->detect_total_money($orders['orders'],$orders['custom_orders']);
        $active_profit = financial_reconciliations::query()
            ->where('seller_id','=',auth()->id())
            ->selectRaw('sum(total_money - ( total_money * admin_profit_percentage / 100 )) as total')->first();
        return messages::success_output('',[
           'pending'=>$pending_money,
           'active'=>$active_profit->total
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $output = FinancialRecociliationsWithAllData::get_data()->with('problem')->find($id);
        return FinancialReconciliationResource::make($output);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
