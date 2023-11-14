<?php


namespace App\Http\traits\helpers_requests_api;


use App\Actions\ArticlesWithAllData;
use App\Actions\MyCurrentPoints;
use App\Actions\ProductWithAllData;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\MarketerClientResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\articles;
use App\Models\followers;
use App\Models\marketer_clients;
use App\Models\orders;
use App\Models\packages_orders;
use App\Models\products;
use App\Models\projects;
use App\Models\User;
use App\Services\DB_connections;
use Carbon\Carbon;

trait QuickReportUserHelperApi
{
    use messages;
    public function quick_report($id = null){
        if($id == null){
            if(request()->has('id')){
                $id = request('id');
            }else{
                $id = auth()->id();
            }
        }
        $user = User::query()->where('id','=',auth()->id())->with('role')->first();
        if($user->role->name == 'client'){
            return messages::success_output('',$this->client_report($id));
        }else if($user->role->name == 'seller'){
            return messages::success_output('',$this->seller_report($id));
        }
    }

    public function client_report(){

        $result = [
            'orders'=>orders::query()->where('user_id','=',auth()->id())->count(),
            'following'=>followers::query()->where('user_id','=',auth()->id())->count(),
            'products_fav'=>ProductResource::collection(ProductWithAllData::get()->has('favourite')->get()),
            'articles_fav'=>ArticleResource::collection(ArticlesWithAllData::get()->has('favourite')->get()),
        ];
        return $result;

    }

    public function seller_report($id){
        $products = ProductWithAllData::get()->where('user_id','=',$id)->paginate(10);
        $articles = ArticlesWithAllData::get()->where('user_id','=',$id)->paginate(10);
        $result = [
            'products_count'=>products::query()->where('user_id','=',$id)->count(),
            'followers_count'=>followers::query()->where('following_id',$id)->count(),
            'articles_count'=>articles::query()->where('user_id','=',$id)->count(),
            'products'=>ProductResource::collection($products),
            'articles'=>ArticleResource::collection($articles),
            'user'=>UserResource::make(User::query()->find($id))
        ];
        return $result;
    }

    public function handle_date($result,$data,$client = null){

        $transactions_number = 0;  $branches_count = 0;  $operations_count = 0;
        foreach($data as $d){
            $branches_count += $d->branches_count;
            $operations_count += $d->operations_count;
            foreach($d['operations'] as $operation){
                $transactions_number+= $operation->transactions_count;
            }
        }
        $result['projects'] = sizeof($data);
        $result['branches'] = $branches_count;
        $result['operations'] = $operations_count;
        $result['transactions'] = $transactions_number;
        if($client != null){
            // marketer process to get client info
            $result['client'] = MarketerClientResource::make($client);
        }
        return $result;

    }
}
