<?php

namespace App\Http\Controllers;

use App\Filters\EndDateFilter;
use App\Filters\marketer\StatusFilter;
use App\Filters\marketer\UsernameFilter;
use App\Filters\NameFilter;
use App\Filters\StartDateFilter;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\CategoryHeadingQuestionsDataResource;
use App\Http\traits\messages;
use App\Models\categories;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Http\Request;

class CategoriesControllerResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = categories::query()->withCount('products')->orderBy('id','DESC');

        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                NameFilter::class
            ])
            ->thenReturn()
            ->get();
        return CategoriesResource::collection($output);

    }

    public function cat_questions_features(){
        $data = categories::query()->withCount('products')->with(['features','heading_questions'=>function($e){
            $e->with('questions_data',function($e){
                $e->with('selections')->with('image');
            });
        }])->orderBy('id','DESC');
        if(request()->has('category_id')){
            return CategoriesResource::make($data->find(request('category_id')));
        }else{
            $final = app(Pipeline::class)
                ->send($data)
                ->through([
                    NameFilter::class
                ])
                ->thenReturn()
                ->get();
            return CategoriesResource::collection($final);
        }
        // return messages::error_output('there is no category with this id');
    }


    public function cat_questions()
    {
        $data = categories::query()->with('questions',function($e){
            $e->with(['selections','image']);
        })->where('id','=',request('category_id'))->first();
        if($data != null && sizeof($data->questions) > 0){
            return CategoryHeadingQuestionsDataResource::collection($data->questions);
        }else{
            return messages::success_output('',[]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
