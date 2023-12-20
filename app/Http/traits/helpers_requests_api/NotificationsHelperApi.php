<?php


namespace App\Http\traits\helpers_requests_api;


use App\Filters\marketer\StatusFilter;
use App\Filters\NameFilter;
use App\Http\Requests\notificationTemplateFormRequest;
use App\Http\Requests\sendNotificationFormRequest;
use App\Http\Resources\NotificationJobResource;
use App\Http\Resources\NotificationTempleteResource;
use App\Http\Resources\NotificationTypeResource;
use App\Http\traits\messages;
use App\Models\notifications;
use App\Models\notifications_jobs;
use App\Models\notifications_templates;
use App\Models\notifications_types;
use App\Models\User;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

trait NotificationsHelperApi
{
    public function statistics_notifications(){
        $output = [
          'pending'=>notifications_jobs::query()->where('status','=','pending')->count(),
          'completed'=>notifications_jobs::query()->where('status','=','completed')->count(),
          'deleted'=>notifications_jobs::query()->onlyTrashed()->count()
        ];
        return messages::success_output('',$output);
    }

    public function notifications_jobs()
    {
         $data = notifications_jobs::query()->with(['template','type'])->orderBy('id','DESC');
         if(request()->filled('status') && request('status') == 'deleted'){
             return NotificationJobResource::collection($data->onlyTrashed()->paginate(10));
         }
         $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StatusFilter::class
            ])
            ->thenReturn()
            ->paginate(10);
        return NotificationJobResource::collection($output);
    }
    public function notifications_types(){
        $data = notifications_types::query()->orderBy('id','DESC')->get();
        return NotificationTypeResource::collection($data);
    }

    public function save_notification_type(){
        $data = notifications_types::query()->updateOrCreate([
            'id'=>request('id') ?? null
        ],[
            'name'=>request('name')
        ]);
        return messages::success_output(trans('messages.saved_successfully'),NotificationTypeResource::make($data));
    }

    public function notifications_templates(){
        $final = notifications_templates::query()->with(['notification_type','user'])->orderBy('id','DESC')->get();
        return NotificationTempleteResource::collection($final);
    }

    public function save_notification_template(notificationTemplateFormRequest $request){
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $output = notifications_templates::query()->updateOrCreate([
            'id'=>request('id') ?? null
        ],$data);

        $final = notifications_templates::query()->with(['notification_type','user'])->find($output->id);
        return messages::success_output(trans('messages.saved_successfully'),NotificationTempleteResource::make($final));
    }

    public function send_notification(sendNotificationFormRequest $request){
        if(request()->filled('one_user')){
            notifications::query()->create([
               'sender_id'=>auth()->id(),
               'receiver_id'=>request('receiver_id'),
               'ar_content'=>request('ar_content'),
               'en_content'=>null,
               'url'=>null,
               'seen'=>0,
            ]);

        }else {
            $data = $request->validated();
            $data['status'] = 'pending';
            $output = notifications_jobs::query()->updateOrCreate([
                'id'=>$data['id'] ?? null
            ],$data);
        }
        return messages::success_output(trans('messages.saved_successfully'));
    }
}
