<?php

namespace App\Http\Controllers;

use App\Actions\GetAuthenticatedUser;
use App\Actions\ProductWithAllData;
use App\Actions\SeenItem;
use App\Filters\CategoryIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\IDsFilter;
use App\Filters\NameFilter;
use App\Filters\products\MaxPriceFilter;
use App\Filters\products\MinPriceFilter;
use App\Filters\products\SellerNameFilter;
use App\Filters\StartDateFilter;
use App\Filters\UserIdFilter;
use App\Http\Requests\ProductsFormRequest;
use App\Http\Resources\ProductCenterDataResource;
use App\Http\Resources\ProductPriceChangeResource;
use App\Http\Resources\ProductResource;
use App\Http\traits\messages;
use App\Jobs\sendNotificationsToFollowersJob;
use App\Models\centralized_products_data;
use App\Models\followers;
use App\Models\products;
use App\Models\products_prices;
use App\Repositories\ProductsRepository;
use App\Services\SearchesResults;
use App\Services\users\favourite_toggle;
use App\Services\users\toggle_data;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use App\Http\traits\upload_image;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductsControllerResource extends Controller
{
    use upload_image;
    public function __construct()
    {
         $this->middleware('CheckApiAuth')->only('store');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // all products data info

        $data = products::query()
            ->when(GetAuthenticatedUser::get_info() != null , function ($e){
                $e->with('favourite');
            })
            ->when(GetAuthenticatedUser::get_info() != null && auth()->user()->role->name == 'seller' , function ($e){
                $e->where('user_id','=',auth()->id());
            })
            ->withCount('likes')
            ->with(['category','images','user','discounts'=>function($e){
                    $e->whereRaw('CURDATE() >= start_date and CURDATE() <= end_date');
                }
                ,'features.feature.image','answers.question.image']);
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                CategoryIdFilter::class,
                MinPriceFilter::class,
                MaxPriceFilter::class,
                UserIdFilter::class,
                IDsFilter::class,
                NameFilter::class,
                SellerNameFilter::class
            ])
            ->thenReturn()
            ->paginate(10);
        return ProductResource::collection($output);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(ProductsFormRequest $request)
    {
        //
        $data = $request->validated();
        DB::beginTransaction();
        $images = [];
        if(request()->hasFile('images')){
            foreach(request()->file('images') as $img){
                $name = $this->upload($img,'products');
                array_push($images,$name);
            }
        }
        $product_reposit = new ProductsRepository();
        $product_reposit->save_product_main_info($data,$images)
            ->save_product_answers($data['answers'] ?? [])
            ->save_product_discounts($data['discounts'] ?? [])
            ->save_product_features($data['features'] ?? [])
            ->save_product_wholesale_prices($data['wholesale_prices'] ?? [])
            ->save_product_cares($data['cares'] ?? [])
            ->save_product_deliveries($data['deliveries'] ?? [])
            ->save_product_change_price()
            ->save_product_centralized_data();
        DB::commit();
        // get following me
        $following_data = followers::query()->where('following_id','=',auth()->id())->get();
        $msg = [
          'ar'=>'تم نشر منتج جديد من قبل '.auth()->user()->username,
          'en'=>'there is a new product published from '.auth()->user()->username,
        ];
        dispatch(new sendNotificationsToFollowersJob($following_data,$msg,'/following'));
        return messages::success_output(trans('messages.saved_successfully'),$product_reposit->product);

    }


    public function toggle_fav(){
        $product = products::query()->find(request('product_id'));
        if($product != null){
            return toggle_data::toggle_fav($product->id,'product');
        }
        return messages::error_output('product id not found');
    }

    public function toggle_like(){
        $product = products::query()->find(request('product_id'));
        if($product != null){
            return toggle_data::toggle_like($product->id,'products');
        }
        return messages::error_output('product id not found');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ProductWithAllData::get()

            ->when(GetAuthenticatedUser::get_info() != null && auth()->user()->role->name == 'seller' ,
                function ($e){
                    $e->where('user_id','=',auth()->id());
                })
            ->when(GetAuthenticatedUser::get_info() != null && auth()->user()->role->name != 'seller',
                function($we){
                    $we->with('last_order_item.order',function($query){
                        $query->where('user_id','=',auth()->id())->with('shipments_info');
                    });
                })->findOrFail($id);
        if($data != null){
            SeenItem::add($data->id,'products');
        }
        if(request()->has('from_search') && GetAuthenticatedUser::get_info() != null && $data != null){
            SearchesResults::added_to_search($data->id,'products');
        }
        return ProductResource::make($data);
    }

    public function search_center(){
        if(request()->has('name')) {
            $data = centralized_products_data::query()
                ->whereRaw('ar_name LIKE "%' . request('name') . '%" OR en_name LIKE "%' . request('name') . '%" ')->first();
            return messages::success_output('',ProductCenterDataResource::make($data));
        }else{
            return messages::error_output('please send name parameter in your request');
        }
    }

    public function filter_prices_change(){
        if(request()->filled('type') && request()->filled('product_id')){
            $output = products_prices::query()
                ->where('product_id','=',request('product_id'))
                ->when(request()->filled('type') && request('type') == 'month',function($e){
                    $e->whereYear('created_at','=',date('Y'));
                })
                ->when(request()->filled('type') && request('type') == 'year',function($e){
                    $e->selectRaw('SUM(price) as change_price , Year(created_at) as year')
                       ->groupBy(DB::raw('product_id'),DB::raw('Year(created_at)'));
                })
               ->get();
            return $output;
        }
    }

}
