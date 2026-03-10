<?php

namespace App\Commands;

use App\Services\CredentialStore;
use LaravelZero\Framework\Commands\Command;

class UseCommand extends Command
{
    protected $signature = 'use {profile : The profile to switch to}';

    protected $description = 'Switch the default profile';

    public function handle(CredentialStore $credentials): int
    {
        $profile = $this->argument('profile');

        if (! $credentials->profileExists($profile)) {
            $this->error("Profile \"{$profile}\" does not exist.");
            $this->line('Run `there-there profiles` to see available profiles.');

            return self::FAILURE;
        }

        $credentials->setDefaultProfile($profile);

        $credentials->setActiveProfile($profile);
        $workspace = $credentials->getWorkspaceName() ?? $profile;

        $this->info("Switched to profile \"{$profile}\" ({$workspace}).");

        return self::SUCCESS;
    }
}
