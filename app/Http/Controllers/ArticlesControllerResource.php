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
use App\Filters\UserIdFilter;
use App\Http\Requests\articleFormRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CommentResource;
use App\Http\traits\messages;
use App\Models\articles;
use App\Models\articles_comments;
use App\Models\likes;
use App\Models\products;
use App\Services\users\toggle_data;
use TonchikTm\PdfToHtml\Pdf;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
use Illuminate\Pipeline\Pipeline;

class ArticlesControllerResource extends Controller
{
    public function __construct()
    {
        $this->middleware('CheckApiAuth')->only(['store','make_comment','make_like']);
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
                UserIdFilter::class
            ])
            ->thenReturn()
            ->paginate(10);
        return ArticleResource::collection($output);
    }

    public function toggle_fav(){
        $article = articles::query()->find(request('article_id'));
        if($article != null){
            return toggle_data::toggle_fav($article->id,'article');
        }
        return messages::error_output('article id not found');
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

        if(request()->hasFile('file')){
            $file = $request->file('file');
            $fileType = request()->file('file')->extension();
            $content = '';
            if ($fileType === 'pdf') {
                $pdf = new Pdf($file->getPathname());
                $htmlContent = $pdf->getHtml();
            }

            return $content;
        }

        $article = articles::query()->updateOrCreate([
            'id'=>request('id') ?? null
        ],$data);

        if(request()->hasFile('images')){
            foreach(request('images') as $file){
                $image = $this->upload($file,'articles');
                ImageModalSave::make($article->id,'articles','articles/'.$image);
            }
        }
        $article = ArticlesWithAllData::get()->find($article->id);
        return messages::success_output(trans('messages.operation_saved_successfully'),ArticleResource::make($article));
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


    public function save_comment(articleFormRequest $request){
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $art = articles_comments::query()->updateOrCreate([
            'id' => request('id') ?? null
        ], $data);
        $art = articles_comments::query()->with('user')->find($art->id);
        return messages::success_output(trans('messages.operation_saved_successfully'),CommentResource::make($art));
    }

    public function save_like(articleFormRequest $request){
        return toggle_data::toggle_like(request('article_id'),'articles');
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
