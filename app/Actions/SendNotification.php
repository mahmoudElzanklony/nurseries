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
        $user = User::query()->find($id);
        if($user != null)
        {

            // $SERVER_API_KEY ='AAAAKXp515Q:APA91bH3r-ax2gsVq20l8tjee0x19-TyYMfSeYKKjpJx0WPJwDUkmAmMa4rDVfPtpD756khydKkH8EI34wYmYsgxn3OEx90XFElV56QwPEO258tFyXarW9evgTH9jo-1VAe5L-96_NrH';
            $SERVER_API_KEY = 'AAAAWm5fWaw:APA91bHKEop8SAYMKUMcDQB5p9W-rUnmRJoXarUOhSKRHaN_H2S1PH6eVWAXDESFLtNTaWhlBEiuoQ4OSA_OsrVR1tblvGppQesUCCN6xZ-r-l3swAaNJFC0eZNrhw35e_TOcjSHEzw9';
            $token_1 = $user->firebase_token;

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

            $headers = [

                'Authorization: key=' . $SERVER_API_KEY,

                'Content-Type: application/json',

            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);
        }

        return response()->json('success');
        // return redirect('admin/clients');
    }
}
