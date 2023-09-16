<?php


namespace App\Repositories;


use App\Actions\CheckProductSupportDeliveryToUserAddress;
use App\Actions\DefaultAddress;
use App\Actions\DeliveryOfOrder;
use App\Actions\SendNotification;
use App\Http\Controllers\classes\payment\VisaPayment;
use App\Http\traits\messages;
use App\Models\notifications;
use App\Models\orders;
use App\Models\orders_addresses;
use App\Models\orders_items;
use App\Models\orders_items_features;
use App\Models\products;
use App\Models\products_features_prices;
use App\Models\user_addresses;

class OrderRepository
{
    public $order;
    public $default_address;

    public function __construct($default_address)
    {
        $this->default_address = $default_address;
    }

    public  function check_delivery_products($products){
        $err = 0;
        foreach($products as $product){
            if(CheckProductSupportDeliveryToUserAddress::check($product['product_id'],$this->default_address) == false){
                $err++;
                $product = products::query()->select(app()->getLocale().'_name as name')->find($product['product_id']);
                break;
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

    public function init_order($data){
        $order = orders::query()->create([
           'user_id'=>auth()->id(),
           'seller_id'=>$data['seller_id'],
           'payment_method'=>$data['payment_method'] ?? 'visa',
           'has_coupon'=>$data['has_coupon'] ?? 0,
           'seller_profit'=>0,
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

    public function order_items($items){
        $err_quantity = 0;
        $total_price_delivery = 0;
        $total_days_delivery = 0;
        foreach($items as $item){
            $product = products::query()->with(['wholesale_prices','discounts'=>function($e){
                $e->where('start_date','<=',date('Y-m-d'))
                   ->where('end_date','>=',date('Y-m-d'));
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
                    $final_price = $this->handle_final_price($product,$whole_price,$discount);
                    $order_item = orders_items::query()->create([
                        'order_id' => $this->order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $final_price,
                    ]);
                    // get price of delivery , get days of delivery
                    $price_days_delivery = DeliveryOfOrder::get($this->default_address,$item['product_id']);
                    $total_price_delivery += ($price_days_delivery->price ?? 0);
                    $total_days_delivery += ($price_days_delivery->days_delivery ?? 0);

                    // remove from product stock the amount of quantity client take
                    $this->remove_from_stock($product,$item['quantity']);
                    // handle order items features
                    $this->orders_items_features($order_item->id,$product,$discount,$item['features'] ?? []);
                }else{
                    $err_quantity++;
                    $msg = trans('errors.quantity_not_exists_for').' '.$product->{app()->getLocale().'_name'};
                    break;
                }
            }
        }
        if($err_quantity > 0){
            return messages::error_output($msg ?? 'error in quantity');
        }
        $this->order_address($total_price_delivery,round($total_days_delivery/sizeof($items)));

    }

    public function orders_items_features($order_item_id,$product,$discount,$features){
        if(sizeof($features) > 0){
            foreach ($features as $feature){
                $feat = products_features_prices::query()->find($feature['id']);
                $price = $feat->price;
                /*if($price > 0 && $discount > 0){
                    $price = $this->handle_final_price($product,$price,$discount,'feature');
                }*/
                orders_items_features::query()->create([
                    'order_item_id'=>$order_item_id,
                    'product_feature_id'=>$feature['id'],
                    'price'=>$price
                ]);
            }
        }
    }

    protected function wholesale_price_item($product,$client_quantity){
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

    protected function discount_per_product($product){
        $discounts = $product->discounts;
        $dis = 0;
        if(sizeof($discounts) > 0){
            foreach($discounts as $obj){
                $dis = $obj->discount;
            }
        }
        return $dis;
    }

    protected function handle_final_price($product,$wholesale = 0,$discount = 0,$type='product'){
        $price = 0;
        if($type == 'product') {
            $price = $product->main_price;
            if($wholesale > 0){
                $price = $wholesale;
            }
            if($discount > 0){
                $dis_val = ($discount / 100) * $price;
                $price = $price - $dis_val;
            }
        }else if($type == 'feature'){
            $price = $wholesale; // wholesale is the price of feature
            $dis_val = ($discount / 100) * $price;
            $price = $price - $dis_val;
        }
        return $price;

    }

    protected function remove_from_stock($product,$quantity){
        $product->quantity = $product->quantity - $quantity;
        $product->save();

    }
}
