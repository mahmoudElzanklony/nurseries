<?php

namespace App\Http\Controllers;

use App\Http\Requests\careFormRequest;
use App\Http\Resources\CareResource;
use App\Http\traits\messages;
use App\Models\care;
use Illuminate\Http\Request;

class CareControllerResource extends Controller
{
    public function __construct(){
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
        $data = care::query()->orderBy('id','DESC')->get();
        return CareResource::collection($data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(careFormRequest $request)
    {
        //
        $data = $request->validated();
        $output =care::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),$output);

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
