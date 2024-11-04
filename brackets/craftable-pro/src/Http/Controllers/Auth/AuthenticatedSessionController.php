<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use App\Settings\GeneralSettings;
use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Auth\LoginRequest;
use Brackets\CraftablePro\Models\CraftableProUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(LoginRequest $request)
    {
        $data = $request->validated();
        $user = CraftableProUser::whereEmail($data['email'])->first();

        if ($user?->wasInvited()) {
            return redirect()->route('craftable-pro.invite-user.create', $data['email']);
        }

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(app(GeneralSettings::class)->default_route);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('craftable-pro')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
