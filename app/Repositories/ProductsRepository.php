<?php


namespace App\Repositories;


use App\Actions\ImageModalSave;
use App\Models\centralized_products_data;
use App\Models\product_centralized;
use App\Models\products;
use App\Models\products_care;
use App\Models\products_delivery;
use App\Models\products_discount;
use App\Models\products_features_prices;
use App\Models\products_prices;
use App\Models\products_questions_answers;
use App\Models\products_wholesale_prices;

class ProductsRepository
{
    public $product;
    public $come_from_centralized;
    public function save_product_main_info($data,$images = []){
        $data['user_id'] = auth()->id();
        if(array_key_exists('come_from_centralized',$data)) {
            $this->come_from_centralized = $data['come_from_centralized'];
        }

        $product = products::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);

        $this->product = $product;
        // check id is not send so this will be new creation
        if(!(array_key_exists('id',$data))){
            // create new price
            $this->create_change_price();
        }
        if(sizeof($images) > 0){
            foreach($images as $img) {
                ImageModalSave::make($product->id, 'products', 'products/'.$img);
            }
        }
        return $this;
    }

    public function save_product_answers($data){
        if(sizeof($data) > 0){
            foreach($data as $item){
                $item['product_id'] = $this->product->id;
                products_questions_answers::query()->updateOrCreate([
                    'id'=>$item['id'] ?? null,
                ],$item);
            }
        }
        return $this;
    }

    public function save_product_features($data){
        if(sizeof($data) > 0){
            foreach($data as $item){
                $item['product_id'] = $this->product->id;
                products_features_prices::query()->updateOrCreate([
                    'id'=>$item['id'] ?? null,
                ],$item);
            }
        }
        return $this;
    }

    public function save_product_wholesale_prices($data){
        if(sizeof($data) > 0){
            foreach($data as $item){
                $item['product_id'] = $this->product->id;
                products_wholesale_prices::query()->updateOrCreate([
                    'id'=>$item['id'] ?? null,
                ],$item);
            }
        }
        return $this;
    }

    public function save_product_cares($data){
        if(sizeof($data) > 0){
            foreach($data as $item){
                $item['product_id'] = $this->product->id;
                $item['user_id'] = auth()->id();
                $item['type'] = 'seller';
                products_care::query()->updateOrCreate([
                    'id'=>$item['id'] ?? null,
                ],$item);
            }
        }
        return $this;
    }

    public function save_product_discounts($data){
        if(sizeof($data) > 0){
            foreach($data as $item){
                $item['product_id'] = $this->product->id;
                products_discount::query()->updateOrCreate([
                    'id'=>$item['id'] ?? null,
                ],$item);
            }
        }
        return $this;
    }

    public function save_product_deliveries($data){
        foreach($data as $d){
            $d['product_id'] = $this->product->id;
            products_delivery::query()->updateOrCreate([
               'id'=>$d['id'] ?? null
            ],$d);
        }
        return $this;
    }

    public function save_product_change_price(){
        $last_change = products_prices::query()
            ->where('product_id','=',$this->product->id)->orderBy('id','DESC')->first();
        if($last_change != null && ($last_change->price != $this->product->main_price)){
            // create new change at price
            $this->create_change_price();
        }
        return $this;
    }

    public function create_change_price(){
        return products_prices::query()->create([
            'product_id'=>$this->product->id,
            'price'=>$this->product->main_price
        ]);
    }

    public function save_product_centralized_data(){
        $product = products::query()->with(['images','features','answers','discounts'])->find($this->product->id);

        if(isset($this->come_from_centralized) && $this->come_from_centralized > 0){
            // this item come from centralized exist
            product_centralized::query()->updateOrCreate([
                'product_id'=>$this->product->id,
                'center_id'=>$this->come_from_centralized,
            ],[
                'product_id'=>$this->product->id,
                'center_id'=>$this->come_from_centralized,
            ]);
        }else{
            // check this product doesnt belong to any product centralized
            $check_center_exist = product_centralized::query()->where('product_id','=',$this->product->id)->first();
            if($check_center_exist == null) {
                // create new centralized data
                centralized_products_data::query()->firstOrCreate([
                    'product_id' => $this->product->id
                ], [
                    'ar_name' => $this->product->ar_name,
                    'en_name' => $this->product->en_name,
                    'ar_description' => $this->product->ar_description,
                    'en_description' => $this->product->en_description,
                    'data' => json_encode($product->toArray())
                ]);
            }
        }
    }
}
