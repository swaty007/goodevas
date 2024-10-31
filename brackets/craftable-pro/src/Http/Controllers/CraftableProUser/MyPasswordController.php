<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use Brackets\CraftablePro\Models\CraftableProUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class MyPasswordController extends Controller
{
    private CraftableProUser $craftableProUser;

    private function setUser(Request $request)
    {
        $this->craftableProUser = $request->user('craftable-pro');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $this->setUser($request);

        return Inertia::render('CraftableProUser/Password/Edit', [
            'craftableProUser' => $this->craftableProUser,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $this->setUser($request);

        $request->validate([
            'password' => ['required', 'confirmed', Password::default()],
        ]);

        $this->craftableProUser->update([
            'password' => Hash::make($request->get('password')),
        ]);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Password successfully updated')]);
    }
}
