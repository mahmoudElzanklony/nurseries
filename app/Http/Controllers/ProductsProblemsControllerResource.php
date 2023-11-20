<?php

namespace App\Http\Controllers;

use App\Actions\ImageModalSave;
use App\Actions\ProductsProblemsWithAllData;
use App\Filters\custom_orders\SellerNameFilter;
use App\Filters\EndDateFilter;
use App\Filters\marketer\StatusFilter;
use App\Filters\StartDateFilter;
use App\Http\Requests\productProblemFormRequest;
use App\Http\Resources\CustomOrderResource;
use App\Http\Resources\ProductProblemResource;
use App\Models\products_problems;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use App\Http\traits\messages;
use App\Http\traits\upload_image;
class ProductsProblemsControllerResource extends Controller
{
    use messages,upload_image;
    public function __construct()
    {
        $this->middleware('CheckApiAuth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = ProductsProblemsWithAllData::get()->with(['user','product']);
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StatusFilter::class,
                StartDateFilter::class,
                EndDateFilter::class,
            ])
            ->thenReturn()
            ->paginate(10);
        return ProductProblemResource::collection($output);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(productProblemFormRequest $request)
    {
        //
        $data = $request->validated();
        $data['status'] = 'pending';
        $data['user_id'] = auth()->id();
        $output = products_problems::query()->updateOrCreate([
            'id'=>request('id') ?? null
        ],$data);
        if(request()->hasFile('images')){
            foreach(request('images') as $file){
                $image = $this->upload($file,'problems');
                ImageModalSave::make($output->id,'products_problems','problems/'.$image);
            }
        }
        $final = ProductsProblemsWithAllData::get()->find($output->id);
        return ProductProblemResource::make($final);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
