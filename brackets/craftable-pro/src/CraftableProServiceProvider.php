<?php

namespace Brackets\CraftablePro;

use Brackets\CraftablePro\Commands\CrudGeneratorCommand;
use Brackets\CraftablePro\Commands\GenerateLocaleTranslationsCommand;
use Brackets\CraftablePro\Commands\GeneratePermissionTranslationsCommand;
use Brackets\CraftablePro\Commands\InstallAdvancedLoggerCommand;
use Brackets\CraftablePro\Commands\InstallCommand;
use Brackets\CraftablePro\Commands\PublishTranslationsCommand;
use Brackets\CraftablePro\Commands\ScanAndSaveTranslationsCommand;
use Brackets\CraftablePro\Commands\SeedCraftableProUserCommand;
use Brackets\CraftablePro\Commands\TestDbConnectionCommand;
use Brackets\CraftablePro\Http\Controllers\Auth\AuthenticatedSessionController;
use Brackets\CraftablePro\Http\Controllers\Auth\ConfirmablePasswordController;
use Brackets\CraftablePro\Http\Controllers\Auth\EmailVerificationNotificationController;
use Brackets\CraftablePro\Http\Controllers\Auth\EmailVerificationPromptController;
use Brackets\CraftablePro\Http\Controllers\Auth\NewPasswordController;
use Brackets\CraftablePro\Http\Controllers\Auth\PasswordResetLinkController;
use Brackets\CraftablePro\Http\Controllers\Auth\RegisteredUserController;
use Brackets\CraftablePro\Http\Controllers\Auth\VerifyEmailController;
use Brackets\CraftablePro\Http\Controllers\CraftableProUser\CraftableProUserController;
use Brackets\CraftablePro\Http\Controllers\CraftableProUser\CraftableProUserInvitationController;
use Brackets\CraftablePro\Http\Controllers\CraftableProUser\MyPasswordController;
use Brackets\CraftablePro\Http\Controllers\CraftableProUser\MyProfileController;
use Brackets\CraftablePro\Http\Controllers\FileUploadController;
use Brackets\CraftablePro\Http\Controllers\HomeController;
use Brackets\CraftablePro\Http\Controllers\Media\MediaController;
use Brackets\CraftablePro\Http\Controllers\Permissions\PermissionController;
use Brackets\CraftablePro\Http\Controllers\Roles\RoleController;
use Brackets\CraftablePro\Http\Controllers\Settings\SettingsController;
use Brackets\CraftablePro\Http\Controllers\TagsController;
use Brackets\CraftablePro\Http\Controllers\Translations\TranslationsController;
use Brackets\CraftablePro\Http\Controllers\UnassignedMediaController;
use Brackets\CraftablePro\Http\Middleware\Authenticate;
use Brackets\CraftablePro\Http\Middleware\CraftableProHandleInertiaRequests;
use Brackets\CraftablePro\Http\Middleware\EnsureEmailIsVerified;
use Brackets\CraftablePro\Http\Middleware\RedirectIfAuthenticated;
use Brackets\CraftablePro\Http\Middleware\SetLocale;
use Brackets\CraftablePro\Http\Middleware\TrackLastActive;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CraftableProServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('craftable-pro')
            ->hasConfigFile()
            ->hasMigration('create_craftable_pro_password_resets_table')
            ->hasMigration('create_craftable_pro_users_table')
            ->hasMigration('define_roles_and_permissions')
            ->hasMigration('create_language_lines_table')
            ->hasMigration('create_tags_table')
            ->hasMigration('create_media_table')
            ->hasMigration('create_unassigned_media_table')
            ->hasMigration('create_general_settings')
            ->hasCommand(InstallCommand::class)
            ->hasCommand(ScanAndSaveTranslationsCommand::class)
            ->hasCommand(PublishTranslationsCommand::class)
            ->hasCommand(GeneratePermissionTranslationsCommand::class)
            ->hasCommand(GenerateLocaleTranslationsCommand::class)
            ->hasCommand(InstallAdvancedLoggerCommand::class)
            // TODO: just tmp for developing purposes
            ->hasCommand(SeedCraftableProUserCommand::class)
            ->hasCommand(CrudGeneratorCommand::class)
            ->hasCommand(TestDbConnectionCommand::class);

        $this->loadViewsFrom(__DIR__ . '/../resources/views/generator/', 'brackets/craftable-pro');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/email_templates/', 'brackets/craftable-pro/email_templates');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/console', 'brackets/craftable-pro/console');
    }

    public function bootingPackage()
    {
        Route::pushMiddlewareToGroup('craftable-pro-base-middlewares', SetLocale::class);
        Route::pushMiddlewareToGroup('craftable-pro-base-middlewares', TrackLastActive::class);
        Route::pushMiddlewareToGroup('craftable-pro-base-middlewares', config('craftable-pro.handle-inertia-request-class', CraftableProHandleInertiaRequests::class));

        Route::pushMiddlewareToGroup('craftable-pro-auth-middleware', Authenticate::class . ':craftable-pro');

        Route::pushMiddlewareToGroup('craftable-pro-guest-middleware', RedirectIfAuthenticated::class . ':craftable-pro');

        Route::pushMiddlewareToGroup('craftable-pro-verified-middleware', EnsureEmailIsVerified::class);

        Route::pushMiddlewareToGroup('craftable-pro-middlewares', SetLocale::class);
        Route::pushMiddlewareToGroup('craftable-pro-middlewares', TrackLastActive::class);
        Route::pushMiddlewareToGroup('craftable-pro-middlewares', Authenticate::class . ':craftable-pro');
        Route::pushMiddlewareToGroup('craftable-pro-middlewares', EnsureEmailIsVerified::class);
        Route::pushMiddlewareToGroup('craftable-pro-middlewares', config('craftable-pro.handle-inertia-request-class', CraftableProHandleInertiaRequests::class));
    }

    public function packageRegistered()
    {
        $this->mergeConfigFrom($this->package->basePath("/../config/auth.php"), 'auth');
        $this->mergeConfigFrom($this->package->basePath("/../config/filesystems.php"), 'filesystems');
        $this->mergeConfigFrom($this->package->basePath("/../config/translation-loader.php"), 'translation-loader');

        $this->publishes([
            $this->package->basePath('/../resources/js') => resource_path('js/craftable-pro'),
        ], "{$this->package->shortName()}-resources");

        $this->publishes([
            $this->package->basePath('/../database/seeders') => base_path('database/seeders'),
        ], "{$this->package->shortName()}-seeders");

        $this->publishes([
            $this->package->basePath('/Http/Middleware/CraftableProHandleInertiaRequests.php')
            => app_path('/Http/Middleware/CraftableProHandleInertiaRequests.php'),
        ], "craftable-pro-handle-inertia-requests");

        // TODO add prefix craftable-pro to the auth routes

        Route::macro('craftablePro', function (string $baseUrl = 'admin') {
            Route::name('craftable-pro.')->middleware('craftable-pro-base-middlewares')->prefix($baseUrl)->group(function () {
                Route::middleware('craftable-pro-guest-middleware')->group(function () {
                    if (config('craftable-pro.self_registration.enabled', false)) {
                        Route::get('register', [RegisteredUserController::class, 'create'])
                            ->name('register');

                        Route::post('register', [RegisteredUserController::class, 'store'])
                            ->name('register.store');
                    }

                    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                        ->name('login');

                    Route::post('login', [AuthenticatedSessionController::class, 'store']);

                    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                        ->name('password.request');

                    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                        ->middleware(['throttle:6,1'])
                        ->name('password.email');

                    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                        ->name('password.reset');

                    Route::post('reset-password', [NewPasswordController::class, 'store'])
                        ->name('password.update');

                    Route::get('invite-user/{email}', [CraftableProUserInvitationController::class, 'createInviteAcceptationUser'])->name('invite-user.create');
                    Route::post('invite-user', [CraftableProUserInvitationController::class, 'storeInviteAcceptationUser'])->name('invite-user.store');
                });

                Route::middleware('craftable-pro-auth-middleware')->group(function () {
                    // auth
                    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                        ->name('verification.notice');

                    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                        ->middleware(['signed', 'throttle:6,1'])
                        ->name('verification.verify');

                    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                        ->middleware('throttle:6,1')
                        ->name('verification.send');

                    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                        ->name('password.confirm');

                    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
                        ->name('password.confirm.submit');

                    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                        ->name('logout');

                    Route::middleware('craftable-pro-verified-middleware')->group(function () {
                        // upload
                        Route::post('upload', [FileUploadController::class, 'upload'])->name('upload');
                        Route::post('unassigned-media-upload', [UnassignedMediaController::class, 'upload'])->name('unassignedMediaUpload');
                        Route::delete('unassigned-media-destroy/{id}', [UnassignedMediaController::class, 'destroy'])->name('unassignedMediaDestroy');

                        // home
                        Route::get('/', [HomeController::class, 'index'])
                            ->name('home');

                        // dashboard
                        Route::get('/dashboard', [HomeController::class, 'dashboard'])
                            ->name('dashboard');

                        // users crud
                        Route::delete('craftable-pro-users/bulk-destroy', [CraftableProUserController::class, 'bulkDestroy']);
                        Route::resource('craftable-pro-users', CraftableProUserController::class)->parameters([
                            'craftable-pro-users' => 'craftableProUser',
                        ]);
                        Route::post('craftable-pro-users/{craftableProUser}/resend-verification-email', [CraftableProUserController::class, 'resendEmailVerificationNotification']);
                        Route::post('craftable-pro-users/bulk-deactivate', [CraftableProUserController::class, 'bulkDeactivate']);
                        Route::post('craftable-pro-users/bulk-activate', [CraftableProUserController::class, 'bulkActivate']);
                        Route::get('craftable-pro-users/{craftableProUser}/impersonalLogin', [CraftableProUserController::class, 'impersonalLogin'])->name('craftable-pro-user.impersonalLogin');
                        Route::post('craftable-pro-users/invite-user', [CraftableProUserInvitationController::class, 'inviteUser'])->name('craftable-pro-user.invite-user');

                        // user profile

                        Route::get('profile', [MyProfileController::class, 'edit'])->name('craftable-pro-users.profile');
                        Route::put('profile', [MyProfileController::class, 'update'])->name('craftable-pro-users.profile.update');

                        Route::get('password', [MyPasswordController::class, 'edit'])->name('craftable-pro-users.password');
                        Route::put('password', [MyPasswordController::class, 'update'])->name('craftable-pro-users.password.update');

                        // translations management
                        Route::get('translations', [TranslationsController::class, 'index'])->name('translations.index');
                        Route::post('translations/rescan', [TranslationsController::class, 'rescan'])->name('translations.rescan');
                        Route::get('translations/export', [TranslationsController::class, 'export'])->name('translations.export');
                        Route::post('translations/import', [TranslationsController::class, 'import'])->name('translations.import');
                        Route::post('translations/import/conflicts', [TranslationsController::class, 'importResolvedConflicts'])->name('translations.import.conflicts');
                        Route::post('translations/publish', [TranslationsController::class, 'publish'])->name('translations.publish');
                        Route::post('translations/{translation}', [TranslationsController::class, 'update'])->name('translations.update');

                        // tags management
                        Route::post('tags', [TagsController::class, 'store'])->name('tags.store');

                        // media management
                        Route::get('media', [MediaController::class, 'index'])->name('media.index');
                        Route::get('media/images', [MediaController::class, 'images'])->name('media.images');
                        Route::get('media/files', [MediaController::class, 'files'])->name('media.files');
                        Route::post('media/update/{media}', [MediaController::class, 'updateMedia'])->name('media.update');

                        // permissions management
                        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
                        Route::put('permissions', [PermissionController::class, 'update'])->name('permissions.update');

                        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
                        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
                        Route::put('roles/{role}/update', [RoleController::class, 'update'])->name('roles.update');

                        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
                        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
                    });
                });
            });
        });
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param string $path
     * @param string $key
     * @return void
     * @throws BindingResolutionException
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $config->set($key, $this->mergeConfig(
                require $path,
                $config->get($key, [])
            ));
        }
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfig(array $original, array $merging)
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }
}
