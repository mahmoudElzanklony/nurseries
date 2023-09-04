<?php

namespace App\Http\Controllers;

use App\Http\Requests\remoteDBConnectionFormRequest;
use App\Http\Resources\RemoteDBResource;
use App\Services\DB_connections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoteConnectionController extends Controller
{
    //


    public function db_test_connect(remoteDBConnectionFormRequest $formRequest){
        $data = $formRequest->validated();
        DB_connections::connect_to_tanent($data['db_name'],$data);
    }

    public function get_con(){
        return  Config::get('database.connections.tenant.driver');
    }

    public function show_tables(){
        DB_connections::connect_to_tanent(request('db_name'),request()->all());
        if($this->get_con() == 'sqlsrv') {
            $data = DB::select('SELECT * FROM '.request('db_name').'.TABLES');
        }else{
            $data = DB::select('SHOW TABLES FROM ' . request('db_name'));
        }
        return $data;
    }

    public function show_columns(){
        DB_connections::connect_to_tanent(request('db_name'),request()->all());
        $data = Schema::getColumnListing(request('table'));
        return $data;
    }
}
