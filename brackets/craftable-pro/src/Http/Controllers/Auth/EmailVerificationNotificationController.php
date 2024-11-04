<?php

namespace Brackets\CraftablePro\Http\Controllers\Auth;

use App\Settings\GeneralSettings;
use Brackets\CraftablePro\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->user('craftable-pro')->hasVerifiedEmail()) {
            return redirect()->intended(app(GeneralSettings::class)->default_route);
        }

        $request->user('craftable-pro')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
