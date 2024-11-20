<?php

namespace App\Http\Requests\CraftablePro\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('global.product.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ext_id' => ['required', Rule::unique(Product::class)],
            'ean' => ['nullable'],
            //            'additional_data' => ['nullable'],
            'product_type_id' => ['required'],
        ];
    }
}
