<?php


namespace App\Actions;


use App\Models\notifications;
use App\Models\User;

class SendNotification
{
    public static function to_admin($sender_id,$info,$url = ''){
        notifications::query()->create([
            'sender_id'=>$sender_id,
            'receiver_id'=>GetFirstAdmin::admin_info()->id,
            'ar_content'=>$info['ar'],
            'en_content'=>$info['en'],
            'url'=>$url,
            'seen'=>'0',
        ]);
        self::send(GetFirstAdmin::admin_info()->id,$info['ar']);

    }

    public static function to_any_one_else_admin($receiver_id,$info,$url = ''){
        notifications::query()->create([
            'sender_id'=>GetFirstAdmin::admin_info()->id,
            'receiver_id'=>$receiver_id,
            'ar_content'=>$info['ar'],
            'en_content'=>$info['en'],
            'url'=>$url,
            'seen'=>'0',
        ]);
        self::send($receiver_id,$info['ar']);
    }

    public  static function send($id , $info,$from = 'Nabta Notification')
    {
        // notification_token
        $user = User::query()->with('devices')->find($id);
        if($user != null)
        {
            $SERVER_API_KEY = 'AAAAF6UF-p0:APA91bHHc5bA553l0PiJWjDQ4bk3xM-ELO0MRNW0bfxv_qlJ7d5jI5-t03lfUsZyOMswOVZQXO4et3T4G_9s2QrEEVf7K5yDVvD9oyed3oJox3Fi16ZBEJBh1XchuKrGX4cAqESrL04P';
            $headers = [

                'Authorization: key=' . $SERVER_API_KEY,

                'Content-Type: application/json',

            ];
            foreach($user->devices as $d){
                $token_1 = $d->notification_token;

                $data = [

                    "registration_ids" => [
                        $token_1
                    ],
                    "data" => ['type' => 'activation'],
                    "notification" => [

                        "title" => $from,

                        "body" => $info,

                        "sound" => "default",// required for sound on ios

                        "image" => "https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=",

                        "click_action"=> "FLUTTER_NOTIFICATION_CLICK"

                    ],

                ];

                $dataString = json_encode($data);



                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                $response = curl_exec($ch);
            }

        }

        return response()->json('success');
        // return redirect('admin/clients');
    }
}
