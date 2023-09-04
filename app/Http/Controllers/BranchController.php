<?php

namespace App\Http\Controllers;

use App\Http\Requests\branchFormRequest;
use App\Http\Resources\BranchResource;
use App\Http\traits\messages;
use App\Models\branches;
use App\Models\projects;
use App\Services\DB_connections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('CheckApiAuth');
    }

    public function index()
    {
        //
        DB_connections::get_wanted_tenant_user();
        if(request()->has('project_id')){
            $branches = branches::query()
                ->with('operations')->with('last_transaction')
                ->withCount('transactions')
                ->where('project_id','=',request('project_id'))
                ->get();
            if(sizeof($branches) > 0){
                return BranchResource::collection($branches);
            }
            return messages::success_output(trans('errors.ops_something_wrong'));
        }
    }

    public function save($data){
        branches::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null,
        ],$data);
        return messages::success_output(trans('messages.branch_saved_successfully'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , branchFormRequest $branchFormRequest)
    {
        //
        $data = $branchFormRequest->validated();
        return $this->save($data);
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
        DB_connections::get_wanted_tenant_user();
        $branch = branches::query()->with('project')->find($id);
        if($branch != null){
            return new BranchResource($branch);
        }
        return messages::success_output(trans('errors.ops_something_wrong'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id , branchFormRequest $branchFormRequest)
    {
        //
        $data = $branchFormRequest->validated();
        return $this->save($data);
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
        // get database that you want to update project inner it
        DB_connections::get_wanted_tenant_user();
        // delete project
        $branch = branches::query()->with('project')->find($id);
        if($branch != null && $branch->project->user_id == auth()->id()){
            $branch->delete();
            return messages::success_output(trans('messages.deleted_successfully'));
        }else{
            return messages::success_output(trans('errors.ops_something_wrong'));
        }
    }
}
