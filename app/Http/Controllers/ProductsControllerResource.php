<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductsFormRequest;
use App\Http\Resources\ProductResource;
use App\Http\traits\messages;
use App\Models\products;
use App\Repositories\ProductsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\traits\upload_image;
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
        //
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
            ->save_product_wholesale_prices($data['wholesale_prices'] ?? []);
        DB::commit();
        return messages::success_output(trans('messages.saved_successfully'),$product_reposit->product);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = products::query()->with(['category','images','wholesale_prices','discounts'
            ,'features'=>function($f){
                $f->with('feature');
            },'answers'=>function($e){
                $e->with('question');
            }])->first();
        return ProductResource::make($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
