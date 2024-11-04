<?php

namespace Brackets\CraftablePro\Http\Controllers\Settings;

use App\Settings\GeneralSettings;
use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Settings\UpdateSettings;
use Brackets\CraftablePro\Translations\TranslationsProcessor;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * @return Response
     *
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('craftable-pro.settings.edit');

        $settings = app(GeneralSettings::class);

        $availableRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return isset($route->action['prefix'])
                && $route->action['prefix'] === '/admin'
                && in_array('GET', $route->methods)
                && Str::between($route->uri, '{', '}') === $route->uri
                && ! in_array($route->uri, [
                    'admin',
                    'admin/login',
                    'admin/forgot-password',
                    'admin/verify-email',
                    'admin/confirm-password',
                    'admin/translations/export',
                ]);
        })->map->uri->values()->toArray();

        return Inertia::render('Settings/Index', [
            'generalSettings' => [
                'available_locales' => $settings->available_locales,
                'default_locale' => $settings->default_locale,
                'default_route' => $settings->default_route,
            ], 'availableRoutes' => $availableRoutes,
        ]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws \InvalidScannerException
     */
    public function update(GeneralSettings $settings, UpdateSettings $request)
    {
        $sanitized = $request->validated();

        $settings->available_locales = $sanitized['available_locales'];
        $settings->default_locale = $sanitized['default_locale'];
        $settings->default_route = $sanitized['default_route'];

        $settings->save();

        if ($request->has('available_locales')) {
            Artisan::call('craftable-pro:generate-locale-translations');
            app(TranslationsProcessor::class)->scanTranslations();
            app(TranslationsProcessor::class)->publishTranslations();
        }

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Settings successfully updated')]);
    }
}
