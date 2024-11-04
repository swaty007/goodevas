<?php

namespace App\Http\Requests\CraftablePro\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('global.product.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ext_id' => ['sometimes'],
            'ean' => ['nullable'],
            'additional_data' => ['nullable'],
            'product_type_id' => ['sometimes'],
        ];
    }
}
