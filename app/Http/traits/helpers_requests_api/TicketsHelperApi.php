<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\Requests\ticketsFormRequest;
use App\Http\Requests\ticketsReplyFormRequest;
use App\Http\Resources\TicketCategoryResource;
use App\Http\Resources\TicketResource;
use App\Http\traits\messages;
use App\Models\reports;
use App\Models\tickets;
use App\Models\tickets_categories;
use App\Models\tickets_messages;
use App\Models\User;

trait TicketsHelperApi
{
    public function get_tickets_cats(){
        $data =  tickets_categories::query()->orderBy('id','DESC')->get();
        return TicketCategoryResource::collection($data);
    }

    public function save_tickets_cats(){
        tickets_categories::query()->updateOrCreate([
            'id'=>request()->has('id') ? request('id') : null
        ],request()->all());
        return messages::success_output(trans('messages.saved_successfully'));
    }


    public function all_tickets(){
        $data = tickets::query()->with('ticket_cat')->orderBy('id','DESC')->get();
        return TicketResource::collection($data);
    }



    public function make_ticket(ticketsFormRequest $ticketsFormRequest){
        $data = $ticketsFormRequest->validated();
        $data['user_id'] = auth()->id();
        $ticket = tickets::query()->updateOrCreate([
            'id'=>request()->has('id') ? request('id') : null,
        ],$data);
        // create report
        /*reports::query()->create([
           'user_id'=>auth()->id(),
           'info'=>trans('keywords.make_ticket_from').auth()->user()->username,
           'type'=>'tickets(make)',
        ]);*/
        return messages::success_output(trans('messages.saved_successfully'),$ticket);

    }

    public function messages(){
        if(request()->has('ticket_id')){
            $messages = tickets_messages::query()
                ->where('ticket_id','=',request('ticket_id'))
                ->get();
            return messages::success_output('',$messages);

        }
    }

    public function reply_ticket(ticketsReplyFormRequest $formRequest){
        $data = $formRequest->validated();
        $user = User::query()->with('role')->find(auth()->id());

        $data['user_id'] = auth()->id();
        $data['status'] = $user->role->name;
        tickets_messages::query()->updateOrCreate([
            'id'=>request()->has('id') ? $data['id']:null,
        ],$data);
        // create report
        reports::query()->create([
            'user_id'=>auth()->id(),
            'info'=>trans('keywords.ticket_replied_from').auth()->user()->username,
            'type'=>'tickets('.$user->role->name == 'admin' ? 'admin':'client'.')',
        ]);
        return messages::success_output(trans('messages.reply_saved_successfully'));

    }
}
