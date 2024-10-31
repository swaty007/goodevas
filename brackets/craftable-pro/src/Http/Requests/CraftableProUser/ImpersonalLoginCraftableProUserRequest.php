<?php

namespace Brackets\CraftablePro\Http\Requests\CraftableProUser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ImpersonalLoginCraftableProUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('craftable-pro.craftable-pro-user.impersonal-login', $this->craftableProUser);
    }

    public function rules(): array
    {
        return [];
    }
}
