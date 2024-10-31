<?php

namespace Brackets\CraftablePro\Http\Controllers\CraftableProUser;

use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\BulkDestroyCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\DestroyCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\ImpersonalLoginCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\IndexCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\StoreCraftableProUserRequest;
use Brackets\CraftablePro\Http\Requests\CraftableProUser\UpdateCraftableProUserRequest;
use Brackets\CraftablePro\Models\CraftableProUser;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Brackets\CraftablePro\Queries\Sorts\SortNullsLast;
use App\Settings\GeneralSettings;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CraftableProUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(IndexCraftableProUserRequest $request): Response | JsonResponse
    {
        $craftableProUsersQuery = QueryBuilder::for(CraftableProUser::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'email_verified_at'
                )),
                AllowedFilter::callback('role', fn (Builder $query, $value) => $query->role($value)),
                AllowedFilter::callback('status', function (Builder $query, $value) {
                    if ($value === "pending") {
                        return $query->whereNull("email_verified_at");
                    } else {
                        return $query->whereActive($value)->whereNotNull("email_verified_at");
                    }
                }),
            ])
            ->defaultSort('id')
            ->allowedSorts(['id', 'first_name', 'email', 'email_verified_at', AllowedSort::custom('last_active_at', new SortNullsLast())]);

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($craftableProUsersQuery->select(['id'])->pluck('id'));
        }

        $craftableProUsers = $craftableProUsersQuery
            ->with('roles')
            ->select(['id', 'first_name', 'last_name', 'email', 'email_verified_at', 'active', 'last_active_at'])
            ->paginate(request()->get('per_page'))
            ->withQueryString();

        return Inertia::render('CraftableProUser/Index', [
            'craftableProUsers' => $craftableProUsers,
            'filterOptions' => [
                'roles' => Role::all()->map->only(['name'])->pluck('name'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(): Response
    {
        $this->authorize('craftable-pro.craftable-pro-user.create');
        $roles = Role::all();

        return Inertia::render('CraftableProUser/Create', [
            'locales' => app(GeneralSettings::class)->available_locales,
            'defaultLocale' => app(GeneralSettings::class)->default_locale,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCraftableProUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreCraftableProUserRequest $request)
    {
        $validated = $request->validated();

        $craftableProUser = CraftableProUser::create($validated);

        $craftableProUser->roles()->sync([$request->input('role_id')]);

        return redirect()->route('craftable-pro.craftable-pro-users.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Display the specified resource.
     *
     * @param CraftableProUser $craftableProUser
     * @return Response
     * @throws AuthorizationException
     */
    public function show(CraftableProUser $craftableProUser)
    {
        $this->authorize('craftable-pro.craftable-pro-user.show', $craftableProUser);

        return Inertia::render('CraftableProUser/Show', [
            'craftableProUser' => $craftableProUser,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CraftableProUser $craftableProUser
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(CraftableProUser $craftableProUser)
    {
        $this->authorize('craftable-pro.craftable-pro-user.edit', $craftableProUser);

        $craftableProUser->load('roles');

        $roles = Role::all();

        return Inertia::render('CraftableProUser/Edit', [
            'craftableProUser' => $craftableProUser,
            'avatar' => $craftableProUser->getMedia('avatar'),
            'locales' => app(GeneralSettings::class)->available_locales,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCraftableProUserRequest $request
     * @param CraftableProUser $craftableProUser
     * @return JsonResponse|RedirectResponse
     */
    public function update(UpdateCraftableProUserRequest $request, CraftableProUser $craftableProUser)
    {
        $validated = $request->validated();

        $craftableProUser->update($validated);

        if ($request->input('role_id')) {
            $craftableProUser->roles()->sync([$request->input('role_id')]);
        }

        if ($request->wantsJson()) {
            return response()->json('success');
        }

        return redirect()->route('craftable-pro.craftable-pro-users.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCraftableProUserRequest $request
     * @param CraftableProUser $craftableProUser
     * @return RedirectResponse
     */
    public function destroy(DestroyCraftableProUserRequest $request, CraftableProUser $craftableProUser)
    {
        $craftableProUser->delete();

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Resend verification notification for user.
     *
     * @param CraftableProUser $craftableProUser
     * @return RedirectResponse
     */
    public function resendEmailVerificationNotification(CraftableProUser $craftableProUser)
    {
        if (! $craftableProUser->hasVerifiedEmail()) {
            if ($craftableProUser->wasInvited()) {
                // FIXME: refactor mailable class
                CraftableProUserInvitationController::sendInvitation(
                    email: $craftableProUser->email,
                    userFullName: Auth::user()->first_name . " " . Auth::user()->last_name,
                );
            } else {
                $craftableProUser->sendEmailVerificationNotification();
            }
        }

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy users.
     *
     * @param BulkDestroyCraftableProUserRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function bulkDestroy(BulkDestroyCraftableProUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    CraftableProUser::whereIn('id', $bulkChunk)->delete();
                });
        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk deactivate users.
     * @param BulkDestroyCraftableProUserRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function bulkDeactivate(BulkDestroyCraftableProUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    CraftableProUser::whereIn('id', $bulkChunk)->update(['active' => false]);
                });
        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk activate users.
     * @param BulkDestroyCraftableProUserRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function bulkActivate(BulkDestroyCraftableProUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    CraftableProUser::whereIn('id', $bulkChunk)->update(['active' => true]);
                });
        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * @param ImpersonalLoginCraftableProUserRequest $request
     * @param CraftableProUser $craftableProUser
     * @return RedirectResponse
     */
    public function impersonalLogin(ImpersonalLoginCraftableProUserRequest $request, CraftableProUser $craftableProUser)
    {
        Auth::login($craftableProUser);

        return redirect()->route('craftable-pro.home');
    }
}
