<?php

namespace App\Http\Controllers;

use App\Http\Requests\cityFormRequest;
use App\Http\Resources\CityResource;
use App\Http\traits\messages;
use App\Models\cities;
use Illuminate\Http\Request;

class CitiesControllerResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = cities::query()->when(request()->has('country_id'),function($e){
            $e->where('country_id','=',request('country_id'));
        })->orderBy('id','DESC')->get();
        return CityResource::collection($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, cityFormRequest $formRequest)
    {
        //
        $data = $formRequest->validated();
        $city = cities::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        $output = cities::query()->with('country')->find($city->id);
        return messages::success_output(trans('messages.saved_successfully'),CityResource::make($output));
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
