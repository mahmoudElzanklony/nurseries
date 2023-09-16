<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ProductsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $status = false;
        if(auth()->check()){
            $user = User::query()->with('role')->find(auth()->id());
            if($user->role->name != 'client'){
                $status = true;
            }
        }
        return $status;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $lang = app()->getLocale();
        $another_lang = ($lang == 'ar'?'en':'ar');
        return [
            'id'=>'filled',
            'category_id'=>'required|exists:categories,id',
            $lang.'_name'=>'required',
            $another_lang.'_name'=>'nullable',
            $lang.'_description'=>'required',
            $another_lang.'_description'=>'nullable',
            'quantity'=>'required',
            'main_price'=>'required|numeric',
            'wholesale_prices'=>'filled|array',
            'wholesale_prices.*'=>'required',
            'answers'=>'required|array',
            'answers.*'=>'required',
            'features'=>'required|array',
            'features.*'=>'required',
            'deliveries'=>'required|array',
            'deliveries.*'=>'required',
            'discounts'=>'filled|array',
            'discounts.*'=>'required',

        ];
    }

    public function messages()
    {
        return [
          'category'=>trans('keywords.category'),
          'ar_name'=>trans('keywords.ar_name'),
          'en_name'=>trans('keywords.en_name'),
          'ar_description'=>trans('keywords.ar_description'),
          'en_description'=>trans('keywords.en_description'),
          'quantity'=>trans('keywords.quantity'),
          'main_price'=>trans('keywords.price'),
          'wholesale_prices'=>trans('keywords.wholesale_prices'),
          'answers'=>trans('keywords.answers'),
          'features'=>trans('keywords.product_features'),
          'discounts'=>trans('keywords.product_discounts'),
        ];
    }
}
