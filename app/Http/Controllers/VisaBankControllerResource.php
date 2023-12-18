<?php

namespace App\Http\Controllers;

use App\Http\Requests\visaFormRequest;
use App\Http\Resources\VisaBankResource;
use App\Http\traits\messages;
use App\Models\users_visa;
use Illuminate\Http\Request;

class VisaBankControllerResource extends Controller
{
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
        $data = users_visa::query()
            ->with('orders.paymentable.items.product')
            ->with('custom_orders.paymentable')
            ->with('packages.paymentable')
            ->orderBy('id','DESC')
            ->where('user_id','=',auth()->id())
            ->get();
        return VisaBankResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(visaFormRequest $request)
    {
        //
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $output = users_visa::query()->with('orders')->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),VisaBankResource::make($output));
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
