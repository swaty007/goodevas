<?php

namespace App\Http\Requests\CraftablePro\Order;

use App\Integrations\Data\Enums\UnifiedFulfilmentStatus;
use App\Integrations\Data\Enums\UnifiedOrderStatus;
use App\Integrations\Data\Enums\UnifiedRefundStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
            // 'order_id' => ['sometimes'],
            // 'type' => ['sometimes'],
            // 'order_date' => ['nullable'],
            // 'update_date' => ['nullable'],
            'order_status' => ['sometimes', Rule::in(array_map(fn ($status) => $status->value, UnifiedOrderStatus::cases()))],
            'fulfillment_status' => ['sometimes', Rule::in(array_map(fn ($status) => $status->value, UnifiedFulfilmentStatus::cases()))],
            'refund_status' => ['sometimes', Rule::in(array_map(fn ($status) => $status->value, UnifiedRefundStatus::cases()))],
            // 'fulfillment' => ['nullable'],
            // 'sales_channel' => ['nullable'],
            // 'total_amount' => ['sometimes'],
            // 'total_currency' => ['sometimes'],
            // 'payment_method' => ['nullable'],
            // 'buyer_name' => ['nullable'],
            // 'address_line_1' => ['nullable'],
            // 'address_line_2' => ['nullable'],
            // 'city' => ['nullable'],
            // 'state' => ['nullable'],
            // 'postal_code' => ['nullable'],
            // 'country_code' => ['nullable'],
            // 'expected_ship_date' => ['nullable'],
            // 'is_shipped' => ['sometimes'],
            //'original_object' => ['nullable'],
            //'orderItems_ids' => ['nullable','array'],
        ];
    }
}
