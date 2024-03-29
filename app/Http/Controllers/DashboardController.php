<?php

namespace App\Http\Controllers;

use App\Actions\ImageModalSave;
use App\Filters\EndDateFilter;
use App\Filters\StartDateFilter;
use App\Filters\UsernameFilter;
use App\Filters\users\RoleIdFilter;
use App\Filters\users\RoleNameFilter;
use App\Http\Requests\adFormRequest;
use App\Http\Requests\categoryQuestionsFeaturesFormRequest;
use App\Http\Resources\AdResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\ads;
use App\Models\categories;
use App\Models\categories_features;
use App\Models\categories_heading_questions;
use App\Models\categories_heading_questions_data;
use App\Models\financial_reconciliations_profit_percentages;
use App\Models\images;
use App\Models\select_options;
use App\Models\taxes;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\traits\helpers_requests_api\PackagesHelperApi;
use App\Http\traits\helpers_requests_api\TicketsHelperApi;
use App\Http\traits\helpers_requests_api\SellersStatisticsHelperApi;
use App\Http\traits\helpers_requests_api\SellersInfoHelperApi;
use App\Http\traits\helpers_requests_api\ClientHelperApi;
use App\Http\traits\helpers_requests_api\ProductsHelperApi;
use App\Http\traits\helpers_requests_api\FinancialHelperApi;
use App\Http\traits\helpers_requests_api\OrdersHelperApi;
use App\Http\traits\helpers_requests_api\NotificationsHelperApi;
use App\Http\traits\helpers_requests_api\DashboardHomeStatistics;
use App\Http\traits\helpers_requests_api\WithdrawMoneyHelperApi;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use App\Http\traits\upload_image;
class DashboardController extends Controller
{
    //
    use upload_image;
    use PackagesHelperApi,TicketsHelperApi,SellersStatisticsHelperApi,SellersInfoHelperApi,DashboardHomeStatistics,
        ProductsHelperApi,FinancialHelperApi,OrdersHelperApi , NotificationsHelperApi , ClientHelperApi , WithdrawMoneyHelperApi;

    public function get_users(){
         $users = User::query()->with('role')
             ->when(request()->filled('role_name') && (request('role_name') == 'client' || request('role_name') == 'company'),function($e){
                 $e->withCount('orders');
             })
             ->when(request()->filled('role_name') && request('role_name') == 'seller' ,function($e){
                 $e->withCount('articles');
             })
             ->orderBy('id','DESC')->when(request()->filled('role_name') && request('role_name'),function($e){
                 $e->withCount('products')->withCount('articles');
             });

        $output = app(Pipeline::class)
            ->send($users)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                UsernameFilter::class,
                RoleNameFilter::class
            ])
            ->thenReturn()
            ->paginate(15);
        return UserResource::collection($output);

    }

    public function save_ad(adFormRequest $request)
    {
        $data = $request->validated();
        $image = $this->upload(request()->file('image'),'ads');
        $output = ads::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        if(request()->filled('id')){
            images::query()
                ->where('imageable_type','=','App\Models\ads')
                ->where('imageable_id','=',request('id'))->delete();
        }
        ImageModalSave::make($output->id,'ads','ads/'.$image);
        return AdResource::make($output);

    }

    public function toggle_block(){
        $output = User::query()->find(request('user_id'));
        $output->block = request('block');
        $output->save();
        return messages::success_output(trans('messages.saved_successfully'),UserResource::make($output));
    }

    public function save_cat(categoryQuestionsFeaturesFormRequest $request)
    {
        DB::beginTransaction();
        $data = $request->validated();
        $category = categories::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],[
            'ar_name'=>$data['ar_name']
        ]);
        foreach(request('features') as $feature){
            $feature['category_id'] = $category->id;
            $fa = categories_features::query()->updateOrCreate([
                'id'=>$feature['id'] ?? null
            ],$feature);
            if(isset($feature['image'])) {
                $image = $this->upload($feature['image'], 'features');
                ImageModalSave::make($fa->id, 'categories_features', 'features/' . $image);
            }
        }
        foreach(request('heading_questions') as $question){
            $heading = categories_heading_questions::query()->updateOrCreate([
                'id'=>$question['id'] ?? null
            ],[
                'category_id'=>$category->id,
                'ar_name'=>$question['ar_name'],
                'en_name'=>null
            ]);
            foreach($question['questions'] as $q){
                $q_data = categories_heading_questions_data::query()->updateOrCreate([
                    'id'=>$q['id'] ?? null
                ],[
                    'category_heading_question_id'=>$heading->id,
                    'ar_name'=>$q['ar_name'],
                    'en_name'=>null,
                    'type'=>$q['type']
                ]);
                // upload image for question
                if(isset($q['image'])) {
                    $image = $this->upload($q['image'], 'questions');
                    ImageModalSave::make($q_data->id, 'categories_heading_questions_data', 'questions/' . $image);
                }
                if($q['type'] != 'text'){
                    foreach($q['options'] as $option){
                        select_options::query()->updateOrCreate([
                            'id'=>$option['id'] ?? null
                        ],[
                            'selectable_id'=>$q_data->id,
                            'selectable_type'=>'App\Models\categories_heading_questions_data',
                            'ar_name'=>$option['ar_name'],
                            'en_name'=>null,
                        ]);
                    }
                }
            }
        }
        DB::commit();
        return messages::success_output(trans('messages.saved_successfully'));
    }

    public function change_tax()
    {
        if(request()->filled('tax')){
            taxes::query()->update(['percentage'=>request('tax')]);
            return messages::success_output(trans('messages.saved_successfully'));
        }else{
            return messages::error_output('tax value not found');
        }
    }

    public function change_profit()
    {
        $from_who = request('from_who');
        $percentage = request('percentage');
        if(request()->filled('percentage') && ( $from_who == 'admin' || $from_who == 'seller')) {
            financial_reconciliations_profit_percentages::query()
                ->where('from_who', '=', $from_who)
                ->update([
                    'percentage'=> $percentage,
                    'note'=>request('note') ?? null
                ]);
            return messages::success_output(trans('messages.saved_successfully'));
        }else{
            return messages::error_output('percentage not sent or from who value is wrong');
        }
    }

    public function profit()
    {
        return financial_reconciliations_profit_percentages::query()->get();
    }


}
