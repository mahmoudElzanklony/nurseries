<?php

namespace App\Http\Controllers;

use App\Actions\ArticlesWithAllData;
use App\Actions\ImageModalSave;
use App\Actions\SeenItem;
use App\Filters\CategoryIdFilter;
use App\Filters\EndDateFilter;
use App\Filters\NameFilter;

use App\Filters\products\SellerNameFilter;
use App\Filters\StartDateFilter;
use App\Filters\TitleFilter;
use App\Http\Requests\articleFormRequest;
use App\Http\Resources\ArticleResource;
use App\Http\traits\messages;
use App\Models\articles;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
use Illuminate\Pipeline\Pipeline;

class ArticlesControllerResource extends Controller
{
    public function __construct()
    {
        $this->middleware('CheckApiAuth')->only('store');
    }

    use upload_image;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = ArticlesWithAllData::get();
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                SellerNameFilter::class,
                TitleFilter::class,
                StartDateFilter::class,
                EndDateFilter::class,

            ])
            ->thenReturn()
            ->paginate(10);
        return ArticleResource::collection($output);
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
    public function store(articleFormRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $article = articles::query()->updateOrCreate([
            'id'=>request('id') ?? null
        ],$data);
        if(request()->hasFile('images')){
            foreach(request('images') as $file){
                $image = $this->upload($file,'articles');
                ImageModalSave::make($article->id,'articles','articles/'.$image);
            }
        }
        return messages::success_output(trans('messages.operation_saved_successfully'),$article);
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
        $article = ArticlesWithAllData::get()->find($id);
        if($article != null){
            SeenItem::add($article->id,'articles');
            return ArticleResource::make($article);
        }else{
            return messages::error_output(trans('errors.not_found_data'));
        }
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
