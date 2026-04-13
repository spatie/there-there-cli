<?php

namespace App\Commands;

use App\Services\CredentialStore;
use LaravelZero\Framework\Commands\Command;

class LogoutCommand extends Command
{
    protected $signature = 'logout {--profile= : Profile to remove} {--all : Remove all profiles}';

    protected $description = 'Clear your stored There There credentials';

    public function handle(CredentialStore $credentials): int
    {
        if ($this->option('all')) {
            $credentials->flushAll();

            $this->info('All profiles removed.');

            return self::SUCCESS;
        }

        $profileOption = $this->option('profile');

        if ($profileOption) {
            $credentials->setActiveProfile($profileOption);
        }

        $profileName = $credentials->getActiveProfileName();

        if (! $credentials->profileExists($profileName)) {
            $this->error("Profile \"{$profileName}\" does not exist.");

            return self::FAILURE;
        }

        $credentials->flush();

        $this->info("Profile \"{$profileName}\" removed.");

        return self::SUCCESS;
    }
}
