<?php


namespace App\Repositories;


use App\Actions\ImageModalSave;
use App\Models\products;
use App\Models\products_delivery;
use App\Models\products_discount;
use App\Models\products_features_prices;
use App\Models\products_questions_answers;
use App\Models\products_wholesale_prices;

class ProductsRepository
{
    public $product;
    public function save_product_main_info($data,$images = []){
        $data['user_id'] = auth()->id();
        $product = products::query()->updateOrCreate([
            'id'=>$data['id'] ?? null
        ],$data);
        $this->product = $product;
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
    }
}
