<?php


namespace App\Repositories;


use App\Actions\CheckPlaceMapLocation;
use App\Actions\CheckProductSupportDeliveryToUserAddress;
use App\Actions\DefaultAddress;
use App\Actions\DeliveryOfOrder;
use App\Actions\PaymentModalSave;
use App\Actions\SendNotification;
use App\Actions\UserCouponModal;
use App\Http\Controllers\classes\payment\VisaPayment;
use App\Http\traits\messages;
use App\Models\coupons;
use App\Models\notifications;
use App\Models\orders;
use App\Models\orders_addresses;
use App\Models\orders_items;
use App\Models\orders_items_features;
use App\Models\products;
use App\Models\products_features_prices;
use App\Models\user_addresses;
use Illuminate\Support\Facades\DB;
use function OpenAI\Responses\Chat\toArray;

class OrderRepository
{
    public $order;
    public $default_address;
    private $payment_data;
    private $deliveries_arr = [];
    public $order_total_price = 0;
    private $coupon;
    private $remove_from_coupon = false;

    public $noon_items_format = [];

    public function __construct($default_address)
    {
        $this->default_address = $default_address;
    }

    public function set_coupon($coupon)
    {
        $this->coupon = $coupon;
    }

    public  function check_delivery_products($products){
        $err = 0;
        foreach($products as $product){
          //  if(CheckProductSupportDeliveryToUserAddress::check($product['product_id'],$this->default_address) == false){
            $delivery = CheckPlaceMapLocation::check_delivery($product['product_id'],$this->default_address);
            if($delivery == false){
                $err++;
                $product = products::query()->select(app()->getLocale().'_name as name')->find($product['product_id']);
                break;
            }else{
                array_push($this->deliveries_arr,$delivery);
            }
        }
        return [
            'error'=>$err,
            'product_name'=>$product->name ?? ''
        ];
    }

    public function validate_payment_info($data){
        $visa_obj = new VisaPayment();
        return $visa_obj->handle($data);
    }

    public function validate_error_coupon($data)
    {
        if(isset($data['has_coupon']) && $data['has_coupon'] != 0){
            $coupon_repos = new CouponRepository();
            $coupon_repos->validate_exist($data['has_coupon']);

            if($coupon_repos->error == ''){
                // no error

                $this->coupon = $coupon_repos->coupon;


            }
            return $coupon_repos->error;
        }
        return '';
    }

    public function init_order($data){
        $this->payment_data = $data['payment_data'] ?? null;
        // check from coupon

        DB::beginTransaction();
        $order = orders::query()->create([
           'user_id'=>auth()->id(),
           'seller_id'=>$data['seller_id'],
           'payment_method'=>$data['payment_method'] ?? 'visa',
           'has_coupon'=>isset($this->coupon) ? 1 : 0,
           'seller_profit'=>0,
           'is_draft'=>$data['is_draft'] ?? 1
        ]);
        $this->order = $order;
        $msg = [
            'ar'=>'تم عمل طلب جديد من قبل '.auth()->user()->username,
            'en'=>'New order has been made from '.auth()->user()->username,
        ];
        // send notification to seller
        SendNotification::to_any_one_else_admin($data['seller_id'],$msg,'/orders');
        // send notification to admin
        SendNotification::to_admin(auth()->id(),$msg,'/orders');
        return $this;
    }


    public function order_address($delivery_days,$delivery_price){
        // get price and days of this order
        // DeliveryOfOrder::get();
        orders_addresses::query()->create([
            'order_id'=>$this->order->id,
            'user_address_id'=>$this->default_address->id,
            'delivery_price'=>$delivery_price,
            'days_delivery'=>$delivery_days,
        ]);
    }

    /**
     * @return mixed
     */
    public function validate_product_for_coupon($product_id)
    {
        if(isset($this->coupon)) {
            if (sizeof($this->coupon['products']) == 0) {
                return true;
            } else {
                $check = false;
                foreach($this->coupon->products as $product){
                    if($product->id == $product_id) {
                        $check = true;
                        break;
                    }
                }
                return $check;
            }
        }
        return false;
    }

    public function order_items($items){
        $err_quantity = 0;
        $total_price_delivery = 0;
        $total_days_delivery = 0;
        foreach($items as $key => $item){
            $product = products::query()->with(['wholesale_prices','discounts'=>function($e){
                $e->whereRaw('CURDATE() BETWEEN start_date AND end_date');
                /*$e->where('start_date','>=',date('Y-m-d'))
                   ->where('end_date','<=',date('Y-m-d'));*/
            }])->find($item['product_id']);
            // check product exist
            if($product != null) {
                // check if user quantity is more than stock
                if($product->quantity >= intval($item['quantity'])) {
                    // check if there is wholesale price
                    $whole_price = $this->wholesale_price_item($product,$item['quantity']); // for example :20
                    // check if there is discount at this date
                    $discount = $this->discount_per_product($product);
                    // handle final price
                    //echo 'quantity ==>'.$item['quantity'] .'<br>';
                    $final_price = $this->handle_final_price($product,$whole_price,$discount,'product',$item['quantity']);
                    // check if this product applied for coupon
                    if($this->validate_product_for_coupon($item['product_id']) == true){
                        $total_price_before_apply_coupon = $final_price;
                        $coupon_value_cash = $final_price * $this->coupon->discount / 100;
                        $final_price -= ($final_price * $this->coupon->discount / 100);
                    }
                    $this->order_total_price += $final_price;
                    //echo 'price of product'.$final_price.' and final now =====> '.$this->order_total_price .'<br>';
                    $order_item = orders_items::query()->create([
                        'order_id' => $this->order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $final_price, // this is total prices for all quantities
                    ]);
                    $noon_item = [
                        'name'=>$product->ar_name,
                        'quantity'=>$item['quantity'],
                        'unitPrice'=>$final_price,
                    ];
                    array_push($this->noon_items_format,$noon_item);
                    // check for apply coupon

                    if($this->validate_product_for_coupon($item['product_id']) == true){

                         UserCouponModal::make($this->coupon->id,$order_item->id,$coupon_value_cash,'orders_items',$total_price_before_apply_coupon);
                         $this->remove_from_coupon = true;
                    }

                    // get price of delivery , get days of delivery
                    if($total_price_delivery < ($this->deliveries_arr[$key]->price ?? 0)){
                        $total_price_delivery = ($this->deliveries_arr[$key]->price ?? 0);
                    }
                    if($total_days_delivery < ($this->deliveries_arr[$key]->days_delivery ?? 0)){
                        $total_days_delivery = ($this->deliveries_arr[$key]->days_delivery ?? 0);
                    }


                    // remove from product stock the amount of quantity client take
                    $this->remove_from_stock($product,$item['quantity']);
                    // handle order items features
                    $this->orders_items_features($order_item->id,$product,$discount,$item['features'] ?? [] , $item['quantity'] ,$this->validate_product_for_coupon($item['product_id']) );

                }else{
                    $err_quantity++;
                    $msg = trans('errors.quantity_not_exists_for').' '.$product->{app()->getLocale().'_name'};
                    break;
                }
            }
        }
        if($this->remove_from_coupon == true){
            $this->remove_from_coupon_quantity();
        }
        if($err_quantity > 0){
            return messages::error_output($msg ?? 'error in quantity');
        }
        // add payment of this order
        $this->order_total_price += $total_price_delivery;
        //ho $this->order_total_price .'<br>';
        //dd($this->order_total_price);
        PaymentModalSave::make($this->order->id,'orders',$this->order_total_price);
        // add address and delivery for this order
        $this->order_address($total_days_delivery,$total_price_delivery);
        DB::commit();

    }

    public function orders_items_features($order_item_id,$product,$discount,$features , $quantity , $coupon_status = false){
        if(sizeof($features) > 0){
            foreach ($features as $feature){
                $feat = products_features_prices::query()->find($feature['id']);
                $price = $feat->price * $quantity;
                /*if($price > 0 && $discount > 0){
                    $price = $this->handle_final_price($product,$price,$discount,'feature');
                }*/
                orders_items_features::query()->create([
                    'order_item_id'=>$order_item_id,
                    'product_feature_id'=>$feature['id'],
                    'price'=>$price,
                ]);
               /* echo "price feature ========>".$price;
                echo "quantity product ========>".$quantity;*/

                $this->order_total_price += $price;
                //echo 'price of feature'.$price.' ==========> total  now'.$this->order_total_price .'<br>';
            }
        }
    }

    public function wholesale_price_item($product,$client_quantity){
        $wholesale_prices_data = $product->wholesale_prices;
        $wholesale_price = $product->main_price;
        if(sizeof($wholesale_prices_data) > 0){
            foreach($wholesale_prices_data as $obj){
                if($client_quantity >= $obj->min_quantity){
                    $wholesale_price = $obj->price;
                }
            }
        }
        return $wholesale_price;
    }

    public function discount_per_product($product){
        $discounts = $product->discounts;
        $dis = 0;
        if(sizeof($discounts) > 0){
            foreach($discounts as $obj){
                $dis = $obj->discount;
            }
        }
        return $dis;
    }

    public function handle_final_price($product,$wholesale = 0,$discount = 0,$type='product',$quantity = 1){
        $price = 0;
        if($type == 'product') {
            $price = $product->main_price;
            if($wholesale > 0 && auth()->user()->role->name != 'client'){
                $price = $wholesale;
            }
            if($discount > 0 && auth()->user()->role->name == 'client'){
                $dis_val = ($discount / 100) * $price;
                $price = $price - $dis_val;
            }
        }else if($type == 'feature'){
            $price = $wholesale; // wholesale is the price of feature
            $dis_val = ($discount / 100) * $price;
            $price = $price - $dis_val;
        }
        //echo 'quantity ==>'.$quantity .'<br>';
        return $price * $quantity;

    }

    protected function remove_from_stock($product,$quantity){
        $pro = products::query()->find($product->id);
        $pro->quantity = $pro->quantity - $quantity;
        $pro->save();

    }

    protected function remove_from_coupon_quantity(){
        if($this->coupon->number > 0){
            coupons::query()->find($this->coupon->id)->update([
                'number'=> $this->coupon->number - 1
            ]);
        }
    }
}
