<?php

namespace App\Jobs;

use App\Actions\ManageTransactionsAction;
use App\Actions\MyCurrentPoints;
use App\Actions\SendNotification;
use App\Models\notifications;
use App\Models\operations;
use App\Models\tenants;
use App\Models\transactions;
use App\Models\User;
use App\Models\website_operation_database_tables_columns;
use App\Repositories\ManagementTransactionsRepository;
use App\Services\DB_connections;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ManageCreateTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public static function do_operation($operation,$db , $period = null){
        $status = ManageTransactionsAction::manage($period->save_type ?? 'create',$operation);
        //  $operation->status == 1
        echo "Start time for operation ".$operation->name." ==> ".Carbon::now()." and save type ==> ". $period->save_type."\n";
        // remove from current points;
        $remove_number = 0;
        DB_connections::connect_to_master();
        $client = User::query()->find($db->user_id);
        auth()->login($client);
        $points = MyCurrentPoints::get();
        //$status = true;
        if($status == true && $points > 0 && $operation->status == 1){


            //

            DB_connections::connect_to_tanent($db->database,[],true);

            $project = ManagementTransactionsRepository::get_project_details($operation->id,$operation->direction);

            if($project != null && $project->branch != null){
                $websites = $project->branch->project->websites;
                $first_website_data = new Collection();
                $last_website_data = new Collection();
                $first_website_id = null;
                $last_website_id = null;
                foreach($websites as $key => $website){
                    DB_connections::connect_to_tanent($db->database,[],true);
                    // get selected table that wanted to take data from
                    $table_info = ManagementTransactionsRepository::get_selected_table($operation,$website);

                    // get selected columns that wanted to take data from
                    $columns_info = ManagementTransactionsRepository::get_selected_columns($operation,$website);
                    $columns_info_for_another_website = ManagementTransactionsRepository::get_selected_columns($operation,$website,'not_equal');
                    // check if connection is database
                    if($website->connection == 'database'){
                        if($website->db_config != null) {
                            // connection in database
                            $third_party_config = [
                                'db_driver' => $website->db_config->db_driver,
                                'db_host' => $website->db_config->db_host,
                                'db_port' => $website->db_config->db_port,
                                'db_username' => $website->db_config->db_username,
                                'db_password' => $website->db_config->db_password,
                            ];
                        }
                        // take data from first website to another
                        if($period != null && $period->save_type == 'create') {
                            $last_transaction = ManagementTransactionsRepository::last_transaction($operation);
                        }else{
                            $last_transaction = null;
                        }
                        $last_transaction_ensureance = true;

                        if($table_info != null && sizeof($columns_info) > 0) {
                            // connect to first website database
                            if (isset($third_party_config)) {
                                DB_connections::connect_to_tanent($website->db_config->db_name, $third_party_config);
                                // ensure that last transaction first_id exists at first website that you will get data from
                                // maybe user change operation table and columns and last transactions first id not exists in
                                // first website you want to get data from

                                if($last_transaction != null) {
                                    $record_check_first_website = DB::table($table_info->name)
                                        ->find($operation->direction == 'one' ? $last_transaction->first_id:$last_transaction->last_id);
                                    if($record_check_first_website == null){
                                        $last_transaction_ensureance = false;
                                    }
                                }
                            } else {
                                continue;
                            }
                        }else{
                            continue;
                        }

                        // check direction of operation
                        if(($operation->direction == 'one' || $operation->direction == 'one_and_two')){
                            if($key == 0) {
                                $first_website_id = $website->id;
                                $first_website_data = ManagementTransactionsRepository::get_data_from_remote_wesite
                                ($table_info, $last_transaction,
                                    $last_transaction_ensureance,$period->start_from,$period->save_type,$operation->direction
                                    ,$points,$operation->query_cond != null && $first_website_id == $operation->query_cond->website_id  ?
                                    $operation->query_cond->where_query:null);
                            }else if($key == 1 && $operation->direction == 'one_and_two'){
                                $last_website_id = $website->id;
                                $last_website_data = ManagementTransactionsRepository::get_data_from_remote_wesite
                                ($table_info, $last_transaction, $last_transaction_ensureance,
                                    $period->start_from,$period->save_type,$operation->direction,$points,
                                    $operation->query_cond != null && $last_website_id == $operation->query_cond->website_id  ?
                                        $operation->query_cond->where_query:null);
                            }
                            echo "Get data from first website".$website->id." ==> ".Carbon::now()."\n";


                        }else{
                            // direction is two
                            if($key == 0) {
                                $last_website_id = $website->id;
                                $last_website_data = ManagementTransactionsRepository::get_data_from_remote_wesite($table_info,
                                    $last_transaction,
                                    $last_transaction_ensureance,$period->start_from,$period->save_type,$operation->direction,
                                    $points,$operation->query_cond != null && $last_website_id == $operation->query_cond->website_id  ?
                                        $operation->query_cond->where_query:null);
                            }
                        }
                        // get data from first website inserted to last website

                        DB_connections::connect_to_tanent($db->database,[],true);
                        if($period->save_type == 'create') {
                            $operation->last_time_create = Carbon::now();
                        }else{
                            $operation->last_time_update = Carbon::now();
                        }
                        $operation->save();
                        // no transactions
                        $middle_website_data = [];
                        $inserted_another_website_data = [];
                        $data_inserted_every_fifty_count = [];
                        $inserted_id_first = 0;

                        // data from first website

                        if(sizeof($first_website_data) > 0 || sizeof($last_website_data) > 0) {
                            $check_enter = false;

                            DB_connections::connect_to_tanent($website->db_config->db_name, $third_party_config);
                            // insert data to website 2 when direction ==> one


                            if(sizeof($first_website_data) > 0 && $key == 1 && $operation->direction == 'one'){
                                // connect to remote database
                                $check_enter = true;
                                // insert data to website 1 when direction ==> two
                            }else if(sizeof($last_website_data) > 0 && $key == 0 && $operation->direction == 'two'){
                                $check_enter = true;
                            }

                            $output_data = sizeof($first_website_data) > 0 ? $first_website_data:$last_website_data;
                            echo "save type =======> ".$period->save_type."\n";
                            echo "output data =======> ".sizeof($output_data)."\n";

                            // check when continue or not
                            // first case from 1 ==> 2 and key is 0     so continue

                            if(sizeof($output_data) > 0){
                                if($operation->direction == 'one' || $operation->direction == 'two'){
                                    if($key == 0){
                                        $first_website_id = $website->id;
                                        continue;
                                    }
                                }
                            }
                            try {
                                $columns_names = array_column(DB::select("SHOW COLUMNS FROM " . $table_info->name), 'Field');
                            }catch (\Throwable $e){
                                $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$table_info->name."'";
                                $columns_names = DB::select($sql);
                                $columns_names = collect($columns_names)->map(function ($e){
                                    return $e->COLUMN_NAME;
                                });
                            }
                            echo "data length ======>".sizeof($output_data)."\n";
                            foreach ($output_data as $k => $item) {

                                $columns_data = [];
                                //  dd($item,$first_website_data);
                                foreach ($columns_info as $key_column => $column) {
                                    $columns_data[$column->name] = $item->{$columns_info_for_another_website[$key_column]->name};
                                }
                                foreach ($columns_names as $c) {
                                    if (!array_key_exists($c, $columns_data)) {
                                        $columns_data[$c] = '';
                                    }
                                }
                                if(in_array('created_at',$columns_names)){
                                    $columns_data['created_at'] = Carbon::now();
                                }
                                if(in_array('updated_at',$columns_names)){
                                    $columns_data['updated_at'] = Carbon::now();
                                }

                                // check data not exist
                                // make check for now is test
                                /*$check = DB::table($table_info->name)
                                    ->where($columns_data)
                                    ->first();*/

                                $check = null;
                                // add to inserted_another_website_data (item) every time
                                array_push($inserted_another_website_data, $columns_data);
                                // every 50 time insert at database

                                if($period->save_type == 'update'){
                                    array_push($data_inserted_every_fifty_count,$item);

                                }else {

                                    if ($k == 0 && $check == null && $period->save_type == 'create') {
                                        try {

                                            $inserted_id = DB::table($table_info->name)
                                                ->insertGetId($columns_data);
                                            $inserted_id_first = $inserted_id;
                                        }catch (\Throwable $e){
                                            echo "error in insert operation =====>".$e->getMessage()."\n";
                                            // disable operation
                                            ManagementTransactionsRepository::disable_operation
                                            ($db,$operation,$e,$client);
                                            break;
                                        }
                                    } else if ($check == null && $period->save_type == 'create') {
                                        // maybe $inserted_id_first is 0 in case check above not null so he will
                                        // go to else if and in this case will be 0

                                        if ($inserted_id_first != 0) {
                                            $inserted_id_first++;
                                            array_push($data_inserted_every_fifty_count, $columns_data);

                                        } else {
                                            // this case $inserted_id_first = 0
                                            // so get $inserted_id_first and insert process and pass push in $data_inserted_every_fifty_count
                                            if ($period->save_type == 'create') {
                                               // echo "insert first item".$columns_data."\n";
                                                $inserted_id = DB::table($table_info->name)
                                                    ->insertGetId($columns_data);
                                                $inserted_id_first = $inserted_id;
                                            }
                                        }
                                    }
                                }
                                // insert every 50 rows
                                if($points < 0){
                                    ManagementTransactionsRepository::disable_operation
                                    ($db,$operation,'',$client,'تم تعطيل العملية بسبب عدم وجود نقاط كافيه يرجي شحن رصيدك و تشغيل العملية مرة اخري');
                                    break;
                                }

                                // ( $k % 500 == 0 || $k == sizeof($output_data) - 1))
                                if ($k > 0  &&  ($k % 500 == 0 || $k == sizeof($output_data) - 1) &&
                                    sizeof($output_data) > 1 && $points > 0) {
                                    if($period->save_type == 'create'){
                                        echo "time to insert 500 or less ". sizeof($data_inserted_every_fifty_count)."\n";
                                        try {
                                            $remove_number = sizeof($output_data);
                                            if(sizeof($output_data) > $points){
                                                // in this case remove from output data items
                                                $data_inserted_every_fifty_count = array_splice($data_inserted_every_fifty_count,0,sizeof($output_data) - $points);
                                                $remove_number = sizeof($output_data) - $points;
                                            }

                                            echo "output data will be inserted nooooooow  =====>".sizeof($data_inserted_every_fifty_count)."\n";
                                            DB::table($table_info->name)
                                                ->insert($data_inserted_every_fifty_count);
                                            // remove from my wallet these points
                                            DB_connections::connect_to_master();
                                            MyCurrentPoints::remove_from_my_acc(sizeof($data_inserted_every_fifty_count));
                                            if(sizeof($output_data) >= $points){
                                                ManagementTransactionsRepository::disable_operation
                                                ($db,$operation,'',$client,'تم تعطيل العمليه رقم '.$operation->id.' تحتاج لشحن رصيدك لمواصلة عملية التزامن بنجاح ');
                                            }
                                            DB_connections::connect_to_tanent($website->db_config->db_name, $third_party_config);
                                            // empty data
                                            $data_inserted_every_fifty_count = [];
                                            // $this->output->writeln('insert at '.$table_info->name);
                                            echo "Insert data at website" . $website->id . " ==> " . Carbon::now() . "\n";
                                        } catch (\Throwable $e) {
                                            // disable operation
                                            echo "error in insert operation =====>".$e->getMessage()."\n";
                                            ManagementTransactionsRepository::disable_operation
                                            ($db,$operation,$e,$client);
                                            break;
                                        }
                                    }else{
                                        $updated_soon = [];
                                        $columns_names_check = $columns_info->map(function ($c){
                                            return $c->name;
                                        });
                                        if(in_array('updated_at',$columns_names)){
                                            DB_connections::connect_to_tanent($db->database, [], true);
                                            $data_to_be_updated = transactions::query()
                                                ->where($operation->direction == 'one'?'first_id':'last_id','>=',$output_data[0]->id)
                                                ->where($operation->direction == 'one'?'first_id':'last_id','<=',$output_data[sizeof($output_data) - 1]->id)
                                                ->get();
                                            foreach ($data_to_be_updated as $data_check){
                                                $check_item = collect($data_inserted_every_fifty_count)->first(function ($q) use ($operation,$data_check){
                                                    // after get data from my middle website sync for loop about it
                                                    //  and get item from remote website that id is matched but updated at for hours not matched
                                                    // this item will be updated for another remote website
                                                    return (
                                                        $q->id ==  $data_check[$operation->direction == 'one'?'first_id':'last_id']
                                                        &&
                                                        Carbon::parse($q->updated_at)->diffInHours(Carbon::parse($data_check[$operation->direction == 'one'?'updated_at':'last_updated_at'])) != 0
                                                    );
                                                });
                                                if($check_item != null){
                                                    array_push($updated_soon,$data_check);
                                                }
                                            }
                                            if(sizeof($updated_soon) > 0){
                                                $ids_to_be_get_from_second_website = collect($updated_soon)->map(function ($q) use ($operation){
                                                    return $q->{$operation->direction == 'one'?'last_id':'first_id'};
                                                });
                                                DB_connections::connect_to_tanent($website->db_config->db_name, $third_party_config);
                                                $data_from_remote_website_updated = DB::table($table_info->name)
                                                    ->whereIn('id',$ids_to_be_get_from_second_website)->get();
                                                $values_updated = [];
                                                foreach($data_from_remote_website_updated as $key_index => $df){
                                                    $middle_website_item = $data_to_be_updated->first(function ($f)use($operation,$df){
                                                        return $f->{$operation->direction == 'one'?'last_id':'first_id'} == $df->id;
                                                    });
                                                    $first_website_item = collect($data_inserted_every_fifty_count)
                                                        ->first(function ($q) use ($operation,$middle_website_item) {

                                                            return (
                                                                $q->id == $middle_website_item[$operation->direction == 'one' ? 'first_id' : 'last_id']
                                                            );
                                                        });

                                                    if(!(in_array('id',$columns_names_check->toArray()))){
                                                        $values_updated[$key_index]['id'] = $df->id;
                                                    }
                                                    foreach($columns_names_check as $c){
                                                        $values_updated[$key_index][$c] = $first_website_item->{$c};
                                                    }
                                                }
                                                if(sizeof($values_updated) > 0){
                                                    // update remote website
                                                    echo "start updated data length ==> ".sizeof($values_updated)."and table name is ".$table_info->name."\n";
                                                    foreach ($values_updated as $i){
                                                        $now_updated = $i;
                                                        unset($now_updated['id']);
                                                        $now_updated['updated_at'] = Carbon::now();
                                                        DB::table($table_info->name)
                                                            ->where('id', $i['id'])
                                                            ->update($now_updated);
                                                    }
                                                    // update in middle website
                                                    DB_connections::connect_to_tanent($db->database,[],true);
                                                    foreach ($values_updated as $i){
                                                        transactions::query()
                                                            ->where($operation->direction == 'one' ? 'last_id':'first_id', $i['id'])
                                                            ->update([
                                                                'updated_at'=>Carbon::now(),
                                                                'last_updated_at'=>Carbon::now(),
                                                            ]);
                                                    }
                                                }

                                            }
                                        }else{
                                            ManagementTransactionsRepository::disable_operation
                                            ($db,$operation,'',$client,'updated at should be found at table to update process done completely');
                                        }
                                        $data_inserted_every_fifty_count = [];
                                    }

                                }


                                // insert in our website (( middle website ))

                                if ($inserted_id_first != 0 && $period->save_type == 'create') {

                                    $item_will_be_created = [
                                        'operation_id' => $operation->id,
                                        'first_website_id' => $first_website_id,
                                        'second_website_id' => $website->id,
                                        'first_id' => $operation->direction == 'one'? $item->id:$inserted_id_first,
                                        'last_id' => $operation->direction == 'one' ? $inserted_id_first:$item->id,
                                        'status' => 1,
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now(),
                                        'last_created_at' => Carbon::now(),
                                        'last_updated_at' => $item->updated_at ?? Carbon::now(),
                                    ]; //
                                    echo "item first_id ==========>".$item_will_be_created['first_id']."\n";
                                    echo "item last id ==========>".$item_will_be_created['last_id']."\n";
                                    echo "\n";

                                    array_push($middle_website_data, $item_will_be_created);
                                }
                            }
                            if($period->save_type == 'create'){
                                //dd($inserted_id_first,$period->save_type,$remove_number,sizeof($middle_website_data));
                                DB_connections::connect_to_tanent($db->database, [], true);
                                if($remove_number > 0){
                                    $middle_website_data = array_splice($middle_website_data,0,$remove_number);
                                    if(sizeof($middle_website_data) > 1000){
                                        $chunk_data = array_chunk($middle_website_data,1000);
                                        foreach($chunk_data as $item_arr){
                                            transactions::query()->insert($item_arr);
                                        }
                                    }else{
                                        transactions::query()->insert($middle_website_data);
                                    }

                                }else {
                                    transactions::query()->insert($middle_website_data);
                                    $chunk_data = array_chunk($middle_website_data,1000);
                                    foreach($chunk_data as $item_arr){
                                        transactions::query()->insert($item_arr);
                                    }
                                }
                                echo "Insert at middle table  ==> " . Carbon::now() . "\n";
                            }
                            $first_website_data = [];
                            $last_website_data = [];
                        }



                    }
                }
            }
        }

    }

    public function handle()
    {
        //
        DB_connections::connect_to_master();
        $allDB = tenants::query()->get();
        foreach($allDB as $db){
            // connect to client database
            DB_connections::connect_to_tanent($db->database,[],true);
            // get all operations for this client
            $operations = operations::query()->with('period',function($p){
                $p->where('save_type','=','create');
            })->orderBy('id','DESC')->get();
            // Run the tasks in parallel
            foreach($operations as $operation){
                self::do_operation($operation,$db);
            }

        }
    }
}
