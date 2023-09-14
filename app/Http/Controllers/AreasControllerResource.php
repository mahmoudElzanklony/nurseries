<?php

namespace App\Http\Controllers;

use App\Http\Requests\areaFormRequest;
use App\Http\Resources\AreaResource;
use App\Http\traits\messages;
use App\Models\areas;
use Illuminate\Http\Request;

class AreasControllerResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $areas = areas::query()->when(request()->has('city_id'),function($e){
            $e->where('city_id','=',request('city_id'));
        })->orderBy('id','DESC')->get();
        return AreaResource::collection($areas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(areaFormRequest $request)
    {
        //
        $data = $request->validated();
        $output = areas::query()->updateOrCreate([
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
