<?php

namespace App\Http\Requests\CraftablePro\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class BulkUpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('global.order.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'ids' => ['required', 'array'],
        ],
            (new UpdateOrderRequest)->rules(),
        );
    }
}
