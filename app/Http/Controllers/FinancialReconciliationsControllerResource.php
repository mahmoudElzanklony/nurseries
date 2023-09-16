<?php

namespace App\Http\Controllers;

use App\Actions\FinancialRecociliationsWithAllData;
use App\Filters\EndDateFilter;

use App\Filters\financial\SellerId;
use App\Filters\StartDateFilter;

use App\Filters\UserIdFilter;
use App\Http\Requests\financialReconciliationFormRequest;
use App\Http\Resources\FinancialReconciliationResource;
use App\Http\traits\messages;
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
                SellerId::class
            ])
            ->thenReturn()
            ->paginate(10);
        return FinancialReconciliationResource::collection($output);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(financialReconciliationFormRequest $request)
    {
        //
        $data = $request->validated();
        $financil_repo = new FinancialReconciliationsRepository();
        return $financil_repo->store_data($data);
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
