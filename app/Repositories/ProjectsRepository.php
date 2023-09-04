<?php


namespace App\Repositories;


use App\Actions\ParametersAction;
use App\Models\parameters;
use App\Models\project_websites_info;
use App\Models\projects;
use App\Models\website_connection_api;
use App\Models\website_connection_database;

class ProjectsRepository
{
    private $project;

    public function create_initial_project($data){
        $p = projects::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null
        ],$data);
        $this->project = $p;
        return $this;
    }

    public function save_project_website_infos($data){
        if(sizeof($data) > 0){
            foreach($data as $key => $info){
                $output = [];
                $output['project_id'] = $this->project->id;
                $output['url'] = $info['url'];
                $output['connection'] = $info['connection'];
                $website = project_websites_info::query()->updateOrCreate([
                    'id'=>array_key_exists('id',$info) ? $info['id']:null
                ],$output);
                $info['data']['website_id'] = $website->id;
                // create info related to database
                if($output['connection'] == 'database'){
                    $this->save_project_database_info($info['data']);
                }else{
                    // create info related to api
                    $this->save_project_api_info($info['data']);
                }
            }
        }
        return $this;
    }

    public function save_project_database_info($data){
        website_connection_database::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null
        ],$data);
        return $this;
    }

    public function save_project_api_info($data){
        $api_website = website_connection_api::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null
        ],$data);
        if(array_key_exists('parameters',$data)){
            foreach($data['parameters'] as $parameter){
                ParametersAction::save_parameter($api_website,$parameter);
            }
        }
        return $this;
    }

}
