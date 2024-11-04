<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use App\Settings\GeneralSettings;
use Brackets\CraftablePro\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user('craftable-pro')->hasVerifiedEmail()) {
            return redirect()->intended(app(GeneralSettings::class)->default_route.'?verified=1');
        }

        if ($request->user('craftable-pro')->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(app(GeneralSettings::class)->default_route.'?verified=1');
    }
}
