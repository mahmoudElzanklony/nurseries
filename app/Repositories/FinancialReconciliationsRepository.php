<?php


namespace App\Repositories;


use App\Actions\DetectRole;
use App\Models\custom_orders;
use App\Models\custom_orders_sellers;
use App\Models\financial_reconciliations;
use App\Models\financial_reconciliations_profit_percentages;
use App\Models\orders;
use App\Models\orders_items_features;
use App\Models\User;

class FinancialReconciliationsRepository
{
    public $financial_obj;
    public function get_orders_to_be_financial($completed = true , $user_id = null , $financial_id = null , $more_info = false){
        if($user_id == null){
            $user_id = auth()->id();
        }
        $orders = orders::query()->when($user_id != null && $user_id > 0 , function ($e) use ($user_id){
                     $e->where('seller_id','=',$user_id);
                  })
                  ->when($financial_id != null , function ($f) use ($financial_id){
                      $f->where('financial_reconciliation_id','=',$financial_id);
                  })
                  ->whereRaw('financial_reconciliation_id is null')

                  ->when($completed == true , function($e){
                      $e->whereHas('last_shipment_info',function($q){
                          $q->where('content','=','completed');
                      });
                  })->with('items',function($e) use ($more_info){
                     $e->when($more_info == true,function($e){
                         $e->with(['product'=>function ($e) {
                             $e->with(['problems','images','features.feature.image','answers'=>function($e){
                                 $e->with('question');
                             }]);
                         }]);
                     })->with(['cancelled']);
                  })->with('payment')->get();

        $custom_orders = custom_orders_sellers::query()
            ->where('client_reply','=','accepted')
            ->whereHas('order',function($e){
                $e->whereRaw('financial_reconciliation_id is null')->whereDoesntHave('cancelled');
            })
            ->when($financial_id == null , function ($e) {
                $e->whereHas('order',function($e){
                    $e->whereRaw('financial_reconciliation_id is null');
                });
            })
            ->when($completed == true , function($e){
                $e->whereHas('order',function($e) {
                    $e->where('status', '=', 'completed');
                });
            })
            ->when($user_id != null && $user_id > 0 , function ($e) use ($user_id){
                $e->where('seller_id','=',$user_id);
            })
            ->when($financial_id != null , function ($f) use ($financial_id){
                $f->where('financial_reconciliation_id','=',$financial_id);
            })
            ->where('status','!=','cancelled')
            ->with(['order.payment'])->whereHas('reply',function($q){
                $q->where('client_reply','=','accepted');
            })->orderBy('id','DESC')->get();
        if(sizeof($custom_orders) > 0){
            $custom_orders = $custom_orders->map(function($e){
                return $e->order;
            });
        }
        return [
          'orders'=>$orders,
          'custom_orders'=>$custom_orders,
        ];
    }

    public function detect_total_money($orders,$custom){
        $total_money = 0;
        foreach($orders as $order){
            $cancel = 0;
            foreach($order->items as $item){
                if($item->cancelled != null && $item->cancelled->type == 'order'){
                    $price = $item->quantity * $item->price;
                    $features = orders_items_features::query()->where('order_item_id','=',$item->id)->get();
                    foreach($features as $feature){
                        $price += $feature->price;
                    }
                    $cancel += $price;
                }
            }
            if($order->payment != null) {
                $total_money += $order->payment->money;
            }
            $total_money -= $cancel;
        }
        foreach($custom as $order){
            if($order->payment != null) {
                $total_money += $order->payment->money;
            }
        }

        return $total_money;
    }

    public function store_data($orders,$custom,$financial_id = null,$seller_id = null){

        $total_money = 0;
        foreach($orders as $order){
            $cancel = 0;
            foreach($order->items as $item){
                if($item->cancelled != null && $item->cancelled->type == 'order'){
                    $price = $item->quantity * $item->price;
                    $features = orders_items_features::query()->where('order_item_id','=',$item->id)->get();
                    foreach($features as $feature){
                        $price += $feature->price;
                    }

                    $cancel += $price;
                }
            }
            if($order->payment != null) {
                $total_money += $order->payment->money;
            }

            $total_money -= $cancel;
        }
        foreach($custom as $order){
            if($order->payment != null) {
                $total_money += $order->payment->money;
            }
        }
        $percentages = financial_reconciliations_profit_percentages::query()->where('from_who', '=', auth()->user()->role->name)->first();
        if($financial_id != null){
            $finan_obj = financial_reconciliations::query()->find($financial_id);
            $finan_obj->status = 'completed';
            $finan_obj->save();
        }else {
            if($seller_id != null && auth()->user()->role->name == 'admin'){
                $seller_id = $seller_id;
            }else{
                $seller_id = auth()->id();
            }
            $finan_obj = financial_reconciliations::query()->create([
                'user_id' => auth()->id(),
                'seller_id' => $seller_id,
                'total_money' => $total_money,
                'admin_profit_percentage' => $percentages->percentage,
                'status' => auth()->user()->role->name == 'seller' ? 'pending' : 'completed'
            ]);
        }
        $this->financial_obj = $finan_obj;
        if(sizeof($orders) > 0){
            $orders_ids = $orders->map(function($e){
                return $e->id;
            });
        }
        if(sizeof($custom) > 0){
            $custom_ids = $custom->map(function($e){
                return $e->id;
            });
        }

        $this->relate_financial_to_orders($finan_obj->id,$orders_ids ?? [],$custom_ids ?? []);

    }

    public function relate_financial_to_orders($financial_id , $orders,$custom_ids){
        if(sizeof($orders) > 0){
            orders::query()->whereIn('id',$orders)->update([
                'financial_reconciliation_id'=>$financial_id
            ]);
        }
        if(sizeof($custom_ids) > 0) {
            custom_orders::query()->whereIn('id', $custom_ids)->update([
                'financial_reconciliation_id' => $financial_id
            ]);
        }
    }

    public function change_status_of_financial($financial_id , $status){
        $obj = financial_reconciliations::query()->find($financial_id);
        $obj->status = $status;
        $obj->save();
        if($status != 'accepted'){
            $orders = orders::query()->where('financial_reconciliation_id','=',$financial_id)->update([
                'financial_reconciliation_id'=>null
            ]);
        }
    }

}
