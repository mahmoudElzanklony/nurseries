<?php

namespace App\Http\Controllers;

use App\Http\Requests\addressesFormRequest;
use App\Http\Resources\UserAddressesResource;
use App\Http\traits\messages;
use App\Models\user_addresses;
use Illuminate\Http\Request;

class UsersAddressControllerResource extends Controller
{
    public function __construct(){
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
        $data = user_addresses::query()
            ->where('user_id','=',auth()->id())
            ->orderBy('id','DESC')->get();
        return $data;
        return UserAddressesResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(addressesFormRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        if(request('default_address') == 1) {
            user_addresses::query()
                ->where('user_id','=',auth()->id())->update(['default_address' => 0]);
        }

        $output = user_addresses::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),UserAddressesResource::make($output));
    }

    public function set_to_default(){
        if(request()->has('id')){
            user_addresses::query()->where('user_id','=',auth()->id())->update([
                'default_address'=>0
            ]);
            try {
                $output = user_addresses::query()
                    ->where('user_id',auth()->id())
                    ->find(request('id'));
                $output->default_address = 1;
                $output->save();
                return messages::success_output(trans('messages.saved_successfully'),UserAddressesResource::make($output));
            }catch (\Throwable $e){
                return messages::error_output($e->getMessage());
            }

        }
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
