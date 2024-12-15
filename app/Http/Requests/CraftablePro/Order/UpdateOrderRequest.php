<?php

namespace App\Http\Requests\CraftablePro\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateOrderRequest extends FormRequest
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
        return [
            'order_id' => ['sometimes'],
            'type' => ['sometimes'],
            'order_date' => ['nullable'],
            'update_date' => ['nullable'],
            'order_status' => ['sometimes'],
            'fulfillment' => ['nullable'],
            'sales_channel' => ['nullable'],
            'total_amount' => ['sometimes'],
            'total_currency' => ['sometimes'],
            'payment_method' => ['nullable'],
            'buyer_name' => ['nullable'],
            'address_line_1' => ['nullable'],
            'address_line_2' => ['nullable'],
            'city' => ['nullable'],
            'state' => ['nullable'],
            'postal_code' => ['nullable'],
            'country_code' => ['nullable'],
            'expected_ship_date' => ['nullable'],
            'is_shipped' => ['sometimes'],
            //'original_object' => ['nullable'],
            //'orderItems_ids' => ['nullable','array'],
        ];
    }
}
