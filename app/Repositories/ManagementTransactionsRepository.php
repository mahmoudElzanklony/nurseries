<?php


namespace App\Repositories;


use App\Actions\SendNotification;
use App\Models\operations;
use App\Models\transactions;
use App\Models\User;
use App\Models\website_operation_database_tables_columns;
use App\Services\DB_connections;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class ManagementTransactionsRepository
{
    public static function get_project_details($operation_id , $direction = ''){
        return  operations::query()->with('branch',function($b) use ($direction){
            $b->with('project',function ($p) use ($direction){
                $p->with('websites',function ($w) use ($direction){
                    $w->when($direction != '' && $direction == 'two',function ($d){
                        $d->orderBy('id','DESC');
                    })->with('db_config','api_config');
                });
            });
        })->find($operation_id);
    }

    public static function get_selected_table($operation,$website){
        return website_operation_database_tables_columns::query()
            ->where('operation_id','=',$operation->id)
            ->where('website_id','=',$website->id)
            ->where('type','=','table')->first();
    }

    public static function get_selected_columns($operation,$website,$status = ''){
        return website_operation_database_tables_columns::query()
            ->where('operation_id','=',$operation->id)
            ->when($status == '' , function ($e) use ($website){
               $e->where('website_id','=',$website->id);
            })
            ->when($status != '' , function ($e) use ($website){
                $e->where('website_id','!=',$website->id);
            })
            ->where('type','=','column')->get();
    }

    public static function last_transaction($operation){
        return transactions::query()
            ->where('operation_id','=',$operation->id)
            ->orderBy('id','DESC')
            ->first();
    }

    public static function get_data_from_remote_wesite($table_info,
                                                       $last_transaction,
                                                       $last_transaction_ensureance,
                                                       $start_from = null,
                                                       $save_type = 'create',
                                                       $direction = 'one',
                                                       $limit = 0,
                                                       $condition = null
    ){
        try{
            $columns_names = array_column(DB::select("SHOW COLUMNS FROM ".$table_info->name), 'Field');
        }catch (\Throwable $e){
            // sql query
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$table_info->name."'";
            $columns_names = DB::select($sql);
            $columns_names = collect($columns_names)->map(function ($e){
               return $e->COLUMN_NAME;
            });

        }

        try{
            // mysql
            $output  = $output = DB::table($table_info->name)
                ->when($last_transaction != null &&
                    $last_transaction_ensureance == true,
                    function ($e) use ($last_transaction,$direction){
                        // get data > first id
                        $e->where('id','>',$direction == 'one' ? $last_transaction->first_id:$last_transaction->last_id);
                    })->when($start_from != null  &&
                    (in_array('created_at',$columns_names) || in_array('updated_at',$columns_names)),
                    function ($q) use ($start_from,$save_type){
                        $q->where($save_type == 'create'?'created_at':'updated_at','>=',$start_from);
                    })
                ->when($condition != null , function ($w) use ($condition){
                    $w->where($condition);
                })
                ->limit($limit)
                ->get();
        }catch (\Throwable $e){
            // sql server
            $output  = $output = DB::table(DB::raw($table_info->name))
                ->selectRaw('top '.$limit.' * ')
                ->when($last_transaction != null &&
                    $last_transaction_ensureance == true,
                    function ($e) use ($last_transaction,$direction){
                        // get data > first id
                        $e->where('id','>',$direction == 'one' ? $last_transaction->first_id:$last_transaction->last_id);
                    })->when($start_from != null  &&
                    (in_array('created_at',$columns_names) || in_array('updated_at',$columns_names)),
                    function ($q) use ($start_from,$save_type){
                        $q->where($save_type == 'create'?'created_at':'updated_at','>=',$start_from);
                    })
                ->when($condition != null , function ($w) use ($condition){
                    $w->where($condition);
                })
                ->get();
        }

        return $output;
    }

    public static function disable_operation($db,$operation,$e,$client,$error = ''){
        DB_connections::connect_to_tanent($db->database, [], true);
        $operation->status = 0;
        $operation->save();
        // send notification to this user
        DB_connections::connect_to_master();
        if($e == ''){
            $err = $error;
        }else{
            $err = $e->getMessage();
        }
        $err_msg_client = 'تم تعطيل العملية ( ' . $operation->name . ' بسبب رساله خطأ ظهرت نصها : '
            . $err . ' يرجي علاج المشكلة واعاده تشغيل العملية مره اخري ';
        $err_msg_admin = 'تم تعطيل العملية رقم ( ' . $operation->id . ' بسبب رساله خطأ ظهرت نصها : '
            . $err . ' لدي العميل ' . $client->username;
        SendNotification::to_any_one_else_admin($db->user_id, $err_msg_client);
        SendNotification::to_admin($client->id, $err_msg_admin);
    }


}
