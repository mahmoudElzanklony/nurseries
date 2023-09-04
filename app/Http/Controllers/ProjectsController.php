<?php

namespace App\Http\Controllers;

use App\Http\Requests\projectsFormRequest;
use App\Http\Resources\ProjectResource;
use App\Http\traits\messages;
use App\Models\projects;
use App\Repositories\ProjectsRepository;
use App\Services\DB_connections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\traits\upload_image;
class ProjectsController extends Controller
{
    use upload_image;
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
        return messages::success_output('',$this->get_projects_data());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectsRepository $repository , projectsFormRequest $request)
    {
        //
        DB::beginTransaction();
        $data = $request->validated();
        if(request()->hasFile('image')){
            $img = $this->upload(request()->file('image'),'projects');
        }else{
            $img = 'default.png';
        }
        // info of project that will be created
        $project_info = [
            'user_id'=>auth()->id(),
            'name'=>$data['name'],
            'info'=>$data['info'],
            'image'=>$img,
        ];
        // get database that you want to create project inner it
        DB_connections::get_wanted_tenant_user();
        // start process of create project
        $repository->create_initial_project($project_info)
                   ->save_project_website_infos($data['projects_connections_data']);
        // return message response of api
        DB::commit();

        return messages::success_output(trans('messages.project_saved_successfully'));


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
        $project = projects::query()->with('websites',function($e){
            $e->with(['db_config','api_config'=>function($query){
                $query->with('parameters');
            }]);
        })->find($id);
        if($project != null) {
            return new \App\Http\Resources\ProjectResource($project);
        }else{
            return messages::error_output(trans('errors.not_found_data'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id , ProjectsRepository $repository ,
                           projectsFormRequest $projectsFormRequest)
    {
        //
        $data = $projectsFormRequest->validated();
        // info of project that will be updated
        $project_info = [
            'id'=>$id,
            'user_id'=>auth()->id(),
            'name'=>$data['name'],
            'info'=>$data['info'],
        ];
        // get database that you want to update project inner it
        DB_connections::get_wanted_tenant_user();
        // start process of update project
        $repository->create_initial_project($project_info)
            ->save_project_website_infos($data['projects_connections_data']);
        // return message status to user
        return messages::success_output(trans('messages.project_saved_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        // get database that you want to update project inner it
        DB_connections::get_wanted_tenant_user();
        // delete project
        $project = projects::query()->find($id);
        if($project != null && $project->user_id == auth()->id()){
            $project->delete();
            return messages::success_output(trans('messages.deleted_successfully'));
        }else{
            return messages::success_output(trans('errors.ops_something_wrong'));
        }

    }


    public function get_projects_data($number = null){
        DB_connections::get_wanted_tenant_user();
        $projects = projects::query()->withCount('operations')
          //  ->whereHas('operations')
            ->with('branches',function($b){
                $b->with('operations')->with('last_transaction')->withCount('transactions');
            })
            ->when(isset($number) && $number != null,function ($q){
                $q->limit(3);
            })
            ->orderBy('id','DESC')
            ->get();
        // return $projects;
        $data =  ProjectResource::collection($projects);
        //  return $data;
        $result = [];
        foreach($data as $d){
            $transaction_count = 0;
            $last_updated_at = date('d/m/Y:H:i:s', strtotime('2020-05-10'));
            foreach($d['branches'] as $t){
                $transaction_count += $t->transactions_count;
                if($t->last_transaction){
                    if($t->last_transaction->created_at->toDateTime() > $last_updated_at){
                        $last_updated_at = $t->last_transaction->created_at->toDateTime();
                    }
                }
                // dd($t->last_transaction->created_at->toDateTime());

            }

            $item = [
                'id'=>$d->id,
                'name'=>$d->name,
                'image'=>$d->image,
                'created_at'=>$d->created_at,
                'branches'=>$d->branches,
                'branches_count'=>sizeof($d->branches),
                'operations_count'=>$d->operations_count,
                'transactions_count'=>$transaction_count,
                'last_update_transaction'=>$last_updated_at,
            ];
            array_push($result,$item);
        }
        return $result;
    }


    public function last(){

        return messages::success_output('',$this->get_projects_data(3));
    }

}
