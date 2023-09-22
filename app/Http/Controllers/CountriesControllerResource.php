<?php

namespace App\Http\Controllers;

use App\Http\Requests\countriesFormRequest;
use App\Http\Resources\CountryResource;
use App\Http\traits\messages;
use App\Models\countries;
use Illuminate\Http\Request;

class CountriesControllerResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = countries::query()->with('image')->get();
        return  CountryResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(countriesFormRequest $request)
    {
        //
        $data = $request->validated();
        $output = countries::query()->updateOrCreate([
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
