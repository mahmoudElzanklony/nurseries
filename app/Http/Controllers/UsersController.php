<?php

namespace App\Http\Controllers;


use App\Actions\ImageModalSave;
use App\Http\Requests\usersFormRequest;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\packages_orders;
use App\Models\projects;
use App\Models\reports;
use App\Models\roles;
use App\Models\transactions;
use App\Models\User;
use App\Services\DB_connections;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\traits\helpers_requests_api\QuickReportUserHelperApi;
use App\Http\traits\helpers_requests_api\MarketerProfitHelperApi;
use App\Http\traits\upload_image;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends Controller
{
    //
    use messages , QuickReportUserHelperApi,MarketerProfitHelperApi,upload_image;

    public function update_personal_info(usersFormRequest $usersFormRequest){
        $data = $usersFormRequest->validated();
        if(request()->hasFile('image')){
            $image = $this->upload(request()->file('image'),'users');
            ImageModalSave::make(auth()->id(),'users','users/'.$image);
        }
        User::query()->where('id',auth()->id())->update($data);
        $output = User::query()->with('image')->find(auth()->id());
        $output['token'] = JWTAuth::fromUser($output);
        return messages::success_output(trans('messages.updated_successfully'),UserResource::make($output));
    }

    public function visit_seller(){
        $id = request('id');
        return $this->seller_report($id);
    }

    public function update_password(usersFormRequest $usersFormRequest){
        // validate password
        $data = $usersFormRequest->validated();
    }

    public function points_transactions(){
        $points = packages_orders::query()
            ->where('user_id','=',auth()->id())
            ->where('status','=',1)
            ->selectRaw('sum(current_points) as points , YEAR(created_at) as year')
            ->groupBy('year')
            ->get();
        DB_connections::get_wanted_tenant_user();

        foreach($points as $p){
            $p->transactions = transactions::query()->select('id')
                ->whereYear('created_at','=',$p->year)->count();
        }


        return messages::success_output('',$points);
    }

}
