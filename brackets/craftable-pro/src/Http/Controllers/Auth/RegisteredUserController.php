<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use App\Settings\GeneralSettings;
use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Auth\RegisterUserRequest;
use Brackets\CraftablePro\Models\CraftableProUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Register', [
            'locales' => app(GeneralSettings::class)->available_locales,
            'defaultLocale' => app(GeneralSettings::class)->default_locale,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @return RedirectResponse
     */
    public function store(RegisterUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = CraftableProUser::create($data);

        $user->assignRole(config('craftable-pro.self_registration.default_role'));

        event(new Registered($user));

        Auth::guard('craftable-pro')->login($user);

        return redirect(app(GeneralSettings::class)->default_route);
    }
}
