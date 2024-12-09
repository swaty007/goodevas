<?php

namespace App\Http\Requests\CraftablePro\ApiKey;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateApiKeyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('global.api-key.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['sometimes'],
            'type' => ['sometimes'],
            'key' => ['sometimes'],
            'additional_data' => ['nullable'],
        ];
    }
}
