<?php

namespace App\Http\Controllers;

use App\Filters\EndDateFilter;
use App\Filters\orders\PaymentTypeFilter;
use App\Filters\StartDateFilter;
use App\Http\Requests\operationFormRequest;
use App\Http\Resources\OperationResource;
use App\Http\traits\messages;
use App\Models\operations;
use App\Models\projects;
use App\Models\transactions;
use App\Repositories\OperationRepository;
use App\Services\DB_connections;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pipeline\Pipeline;

class OperationController extends Controller
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
        DB_connections::get_wanted_tenant_user();
        //
        $operations = operations::query()
            ->withCount('transactions')
            // ->with('last_transaction')
            ->when(request()->has('branch_id'),function ($e){
                $e->where('branch_id','=',request('branch_id'));
            })
            ->orderBy('id','DESC')->paginate(9);
        return OperationResource::collection($operations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , operationFormRequest $operationFormRequest,
                          OperationRepository $repository)
    {
        //
        $data =  $operationFormRequest->validated();
        $repository->create_init_operation($data);

        return messages::success_output(trans('messages.operation_saved_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        DB_connections::get_wanted_tenant_user();
        //
        $operation = operations::query()
            ->with(['database_tables_columns','period','conditions'])->find($id);
        if($operation != null) {
            return new OperationResource($operation);
        }else{
            return messages::error_output(trans('errors.no_data'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id , operationFormRequest $operationFormRequest,
                           OperationRepository $repository)
    {
        //
        $data =  $operationFormRequest->validated();

        $repository->create_init_operation($data);

        return messages::success_output(trans('messages.project_saved_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function report(){
        DB_connections::get_wanted_tenant_user();
        $data = transactions::query()
            ->selectRaw('count(id) as total,status,DATE(created_at) as date')
            ->where('operation_id','=',request('operation_id'))
            ->groupBy('date');

        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
            ])
            ->thenReturn()
            ->get();

        return messages::success_output('',$output);
    }

    public function statistic_report_per_year(){

        DB_connections::get_wanted_tenant_user();
        $data = [];
        for($i = 0; $i < 12; $i++){

            $val = transactions::query()
                ->selectRaw('count(id) as total,status,DATE(created_at) as date')
                ->where('operation_id','=',request('operation_id'))
                ->whereMonth('created_at','=',$i+1)
                ->groupBy('date');

            /*
             ->get()->sum(function ($e){
                    return $e->total;
                });
             **/
            $output = app(Pipeline::class)
                ->send($val)
                ->through([
                    StartDateFilter::class,
                    EndDateFilter::class,
                ])
                ->thenReturn()
                ->get()->sum(function ($e){
                    return $e->total;
                });
            array_push($data,$output);
        }
        return messages::success_output('',$data);
    }

    public function destroy($id)
    {
        //
        // get database that you want to update project inner it
        DB_connections::get_wanted_tenant_user();
        // delete project
        $operation = operations::query()->with('branch',function($q){
                        $q->with('project');
                    })->find($id);
        if($operation != null && $operation->branch->project->user_id == auth()->id()){
            $operation->delete();
            return messages::success_output(trans('messages.deleted_successfully'));
        }else{
            return messages::error_output(trans('errors.ops_something_wrong'));
        }

    }
}
