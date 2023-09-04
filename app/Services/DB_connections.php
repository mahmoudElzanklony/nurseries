<?php


namespace App\Services;


use App\Http\traits\messages;
use App\Models\tenants;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Nette\Schema\ValidationException;
use function PHPUnit\Framework\throwException;

class DB_connections
{
    public static function connect_to_tanent($data_base_name,$third_part = [],$reset_config = false){
        Config::set('database.connections.tenant.database',$data_base_name);
        if(env('APP_ENV') != 'local') {
            // Config::set('database.connections.tenant.username', $data_base_name);
            Config::set('database.connections.tenant.password',env('password_tenant','_2022_'));
        }
        if($reset_config == true){
            Config::set('database.connections.tenant.driver', 'mysql');
            Config::set('database.connections.tenant.host', '127.0.0.1'); // Set the host to the default value
            Config::set('database.connections.tenant.port', '3306'); // Set the port to the default value
            Config::set('database.connections.tenant.username', 'root'); // Set the username to the default value
            Config::set('database.connections.tenant.password', env('db_prod_pass','_2022_')); // Set the password to the default value
        }
        if(sizeof($third_part) > 0){
            Config::set('database.connections.tenant.driver',$third_part['db_driver']);
           // Config::set('database.connections.tenant.url',$data_base_name['url']);
            Config::set('database.connections.tenant.host',$third_part['db_host']);
            Config::set('database.connections.tenant.port',$third_part['db_port']);
            Config::set('database.connections.tenant.username',$third_part['db_username']);
            Config::set('database.connections.tenant.password',$third_part['db_password']);
        }
        DB::connection('tenant')->reconnect();
        DB::setDefaultConnection('tenant');

        try {
            $dbconnect = DB::connection()->getPDO();
            $dbname = DB::connection()->getDatabaseName();
        } catch(Exception $e) {
            dd($e->getMessage());
            return messages::error_output($e);
        }
    }

    public static function connect_to_master(){
        DB::purge('tenant');
        DB::connection('master')->reconnect();
        DB::setDefaultConnection('master');
    }

    public static function create_db($database_name,$info){
        self::connect_to_master();
        // create a copy at table tenant
        tenants::query()->create([
            'database'=>$database_name,
            'username'=>$database_name,
            'password'=>env('password_tenant','_2022_'),
            'user_id'=>$info['user_id'],
            'version'=>$info['version'] ?? 1,
        ]);
        DB::statement('CREATE DATABASE '.$database_name);
        Artisan::call('mig:tenant',['database_name'=>$database_name]);

    }

    public static function get_wanted_tenant_user($id = null){
        $user = Auth::guard('api')->authenticate(request()->header('auth-token'));
        if($user){
            $database_name = tenants::query()->where('user_id', '=', $id ?? $user->id)
                ->first()->database;
            if ($database_name) {
                self::connect_to_tanent($database_name);
            }
        }
        return  \Illuminate\Validation\ValidationException::withMessages(['there is no database for this user']);

    }
}
