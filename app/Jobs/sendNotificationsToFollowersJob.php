<?php

namespace App\Jobs;

use App\Models\notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendNotificationsToFollowersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $data;
    private $content_msg;
    private $url;
    public function __construct($following_data , $msg , $url = '')
    {
        //
        $this->data = $following_data;
        $this->content_msg = $msg;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if(sizeof($this->data) > 0){
            foreach($this->data as $user){
                notifications::query()->create([
                   'sender_id'=>$user->following_id,
                   'receiver_id'=>$user->user_id,
                   'ar_content'=> $this->content_msg['ar'],
                   'en_content'=> $this->content_msg['en'],
                   'url'=> $this->url ?? '',
                   'seen'=>0,
                ]);
            }
        }
    }
}
