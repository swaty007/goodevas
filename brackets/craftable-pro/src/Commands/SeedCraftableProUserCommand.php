<?php

namespace Brackets\CraftablePro\Commands;

use App\Models\Admin;
use Brackets\CraftablePro\Models\CraftableProUser;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedCraftableProUserCommand extends Command
{
    /**
     * @var string
     */
    public $signature = 'craftable-pro:create-admin-user';

    /**
     * @var string
     */
    public $description = 'Create administrator access';

    public function handle(): int
    {
        // TODO consider alerting in production

        $default = 'administrator@finlab.cc';
        $email = $this->option('no-interaction') ? $default : $this->components->ask('Creating an administrator account. Enter an email address (login): ', $default);

        $password = Str::random(12);

        if (class_exists(Admin::class)) {
            $user = Admin::updateOrCreate([
                'email' => $email,
            ], [
                'first_name' => 'Administrator',
                'last_name' => 'Administrator',
                'email' => $email,
                'password' => bcrypt($password),
                'locale' => app(GeneralSettings::class)->default_locale,
            ]);
        } else {
            $user = CraftableProUser::updateOrCreate([
                'email' => $email,
            ], [
                'first_name' => 'Administrator',
                'last_name' => 'Administrator',
                'email' => $email,
                'password' => bcrypt($password),
                'locale' => app(GeneralSettings::class)->default_locale,
            ]);
        }

        $user->markEmailAsVerified();
        $user->assignRole(1);

        $this->components->info("Administrator account was created with credentials (login/password): <fg=green;options=bold>$email</> / <fg=blue;options=bold>$password</> - we recommend to change the password.");

        return self::SUCCESS;
    }
}
