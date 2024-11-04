<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use App\Settings\GeneralSettings;
use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Auth\InviteUserRequest;
use Brackets\CraftablePro\Http\Requests\Auth\InviteUserStoreRequest;
use Brackets\CraftablePro\Mail\InvitationUserMail;
use Brackets\CraftablePro\Models\CraftableProUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CraftableProUserInvitationController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function inviteUser(InviteUserRequest $request)
    {
        $data = $request->validated();

        $user = CraftableProUser::create([
            'email' => $data['email'],
            'password' => bcrypt(Str::random(12)),
            'locale' => app(GeneralSettings::class)->default_locale,
            'active' => false,
            'invitation_sent_at' => now(),
        ])->assignRole($data['role_id']);

        static::sendInvitation(
            email: $data['email'],
            userFullName: Auth::user()->first_name.' '.Auth::user()->last_name
        );

        return redirect()->back()->with(['message' => ___('craftable-pro', 'User was succesfully invited.')]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function createInviteAcceptationUser($email)
    {
        $user = CraftableProUser::whereEmail($email)->firstOrFail();

        if (! $user->wasInvited()) {
            return redirect()->route('craftable-pro.login');
        }

        return Inertia::render('Auth/InviteUser', [
            'email' => $email,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function storeInviteAcceptationUser(InviteUserStoreRequest $request)
    {
        $data = $request->validated();
        $user = CraftableProUser::whereEmail($data['email'])->first();
        $data['password'] = bcrypt($data['password']);
        $user->update($data + ['active' => true, 'invitation_accepted_at' => now()]);
        $user->markEmailAsVerified();

        return redirect()->route('craftable-pro.login');
    }

    public static function sendInvitation(string $email, string $userFullName)
    {
        Mail::to($email)->send(new InvitationUserMail([
            'email' => $email,
            'userFullName' => $userFullName,
        ]));
    }
}
