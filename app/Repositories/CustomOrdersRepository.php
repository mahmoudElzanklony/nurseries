<?php


namespace App\Repositories;


use App\Actions\ImageModalSave;
use App\Actions\SendNotification;
use App\Models\custom_orders;
use App\Models\custom_orders_sellers;

class CustomOrdersRepository
{
    protected $data;
    protected $sellers;
    public $order;
    public function __construct($sellers = [],$data = null){
        $this->sellers = $sellers;
        $this->data = $data;
        if($this->data != null) {
            unset($this->data['sellers']);
        }
    }

    public function init_order($images){
        // create custom order
        $this->order = custom_orders::query()->updateOrCreate([
            'id'=>$this->data['id'] ?? null
        ],$this->data);
        // create images for this custom order
        if(sizeof($images) > 0){
            foreach($images as $img) {
                ImageModalSave::make($this->order->id, 'custom_orders', 'custom_orders/'.$img);
            }
        }
        $admin_info = [
          'ar'=>'تم عمل اوردر خاص من قبل العميل '.auth()->user()->username.' بأسم '.$this->data['name'],
          'en'=>'Custom order has been made by '.auth()->user()->username.' and its name is '.$this->data['name'],
        ];
        $this->send_notification($admin_info);
        return $this;
    }

    public function send_notification($info = [],$another_user_info = []){
        if(sizeof($info) > 0) {
            SendNotification::to_admin(auth()->id(), $info, '/profile/custom-orders');
        }
        if(sizeof($another_user_info) > 0) {
            SendNotification::to_any_one_else_admin($another_user_info['receiver_id'],$another_user_info['info'],'/profile/custom-orders');
        }
    }

    public function send_alerts_to_sellers(){

        foreach($this->sellers as $seller){
            $seller['custom_order_id'] = $this->order->id;
            custom_orders_sellers::query()->updateOrCreate([
               'id'=>$seller['id'] ?? null
            ],$seller);
            $another_user_info = [
                'receiver_id'=>$seller['seller_id'],
                'info'=>[
                    'ar'=>'هناك طلب جديد من العميل '.auth()->user()->username.' وقام بارسال لك طلب بالرد علية اذا كان المنتج متوفر لديك ',
                    'en'=>'New custom order has been made by '.auth()->user()->username.'and send alert to you to reply to this order',
                ]
            ];
            $this->send_notification([],$another_user_info);
        }
    }
}
