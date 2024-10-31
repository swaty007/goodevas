<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Models\CraftableProUser;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     *
     * @return \Inertia\Response
     */
    public function show()
    {
        return Inertia::render('Auth/ConfirmPassword');
    }

    /**
     * Confirm the user's password.
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        /** @var CraftableProUser $user */
        $user = $request->user();

        if (! Auth::guard('craftable-pro')->validate([
            'email' => $user->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(app(GeneralSettings::class)->default_route);
    }
}
