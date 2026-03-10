<?php

namespace App\Commands;

use App\Services\CredentialStore;
use LaravelZero\Framework\Commands\Command;

class UseLocalCommand extends Command
{
    protected $signature = 'use-local';

    protected $description = 'Switch to the local development environment (there-there.test)';

    public function handle(CredentialStore $credentials): int
    {
        $credentials->setBaseUrl('https://there-there.test/api');

        $profileName = $credentials->getActiveProfileName();

        $this->info("Switched to local environment (https://there-there.test/api) for profile \"{$profileName}\".");
        $this->line('Run `there-there login` to authenticate with your local instance.');

        return self::SUCCESS;
    }
}
