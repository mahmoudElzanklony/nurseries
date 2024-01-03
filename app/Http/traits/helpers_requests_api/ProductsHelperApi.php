<?php


namespace App\Http\traits\helpers_requests_api;


use App\Filters\CategoryIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\IDsFilter;
use App\Filters\marketer\StatusFilter;
use App\Filters\NameFilter;
use App\Filters\products\MaxPriceFilter;
use App\Filters\products\MinPriceFilter;
use App\Filters\StartDateFilter;
use App\Filters\UserIdFilter;
use App\Http\Resources\ProductProblemResource;
use App\Http\traits\messages;
use App\Models\products;
use App\Models\products_problems;
use App\Models\products_problems_replies;
use Illuminate\Pipeline\Pipeline;

trait ProductsHelperApi
{
    public function toggle_product(){
        $product = products::query()->find(request('id'));
        if($product->status == 1){
            $product->status = 0;
        }else{
            $product->status = 1;
        }
        $product->save();
        return messages::success_output(trans('messages.saved_successfully'));
    }

    public function update_product_problem_status(){
        $problem = products_problems::query()->find(request('id'));
        if($problem != null){
            $problem->status = request('status');
            $problem->save();
        }
        return messages::success_output(trans('messages.saved_successfully'),ProductProblemResource::make($problem));
    }

    public function problems_statistics(){
        $problems = products_problems::query()->count();
        $pending_problems = products_problems::query()->where('status','=','pending')->count();
        $completed = products_problems::query()->where('status','=','completed')->count();
        $output =  [
          'all'=>$problems,
          'pending'=>$pending_problems,
          'on-progress'=>$completed
        ];
        return messages::success_output('',$output);

    }

    public function all_problems(){
        $products_problems = products_problems::query()
            ->when(request()->filled('product_id'),function($e){
                $e->where('product_id','=',request('product_id'));
            })
            ->with('reply')
            ->with('user')
            ->orderBy('id','DESC');
        $output = app(Pipeline::class)
            ->send($products_problems)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                StatusFilter::class
            ])
            ->thenReturn()
            ->paginate(10);
        return ProductProblemResource::collection($output);
        // product at problem
        // tax , financial per admin , financial per seller
    }

    public function reply_problem(){
        $problem = products_problems::query()->find(request('problem_id'));
        $reply = products_problems_replies::query()->create([
           'user_id'=>auth()->id(),
           'product_problem_id'=>$problem->id,
           'reply'=>request('reply')
        ]);
        if($problem != null){
            $problem->status = 'completed';
            $problem->save();
        }
        return messages::success_output(trans('messages.saved_successfully'),$reply);
    }
}
