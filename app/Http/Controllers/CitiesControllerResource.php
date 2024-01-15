<?php

namespace App\Http\Controllers;

use App\Filters\EndDateFilter;
use App\Filters\NameFilter;
use App\Filters\StartDateFilter;
use App\Filters\UsernameFilter;
use App\Filters\users\RoleNameFilter;
use App\Http\Requests\cityFormRequest;
use App\Http\Resources\CityResource;
use App\Http\traits\messages;
use App\Models\cities;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

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
        })->orderBy('id','DESC');
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                NameFilter::class
            ])
            ->thenReturn()
            ->get();
        return CityResource::collection($output);
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
