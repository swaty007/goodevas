<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use Brackets\CraftablePro\Models\CraftableProUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MyProfileController extends Controller
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

        return Inertia::render('CraftableProUser/Profile/Edit', [
            'craftableProUser' => $this->craftableProUser,
            'locales' => getAvailableLocalesTranslated(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $this->setUser($request);

        $validated = $request->validate([
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'locale' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', Rule::unique('craftable_pro_users', 'email')->ignore($this->craftableProUser->getKey(), $this->craftableProUser->getKeyName())->whereNull('deleted_at')],
        ]);

        $this->craftableProUser->update($validated);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Profile successfully updated')]);
    }
}
