<?php

namespace App\Commands;

use App\Services\CredentialStore;
use LaravelZero\Framework\Commands\Command;

class UseProductionCommand extends Command
{
    protected $signature = 'use-production';

    protected $description = 'Switch to the production environment (there-there.app)';

    public function handle(CredentialStore $credentials): int
    {
        $credentials->setBaseUrl('https://there-there.app/api');

        $profileName = $credentials->getActiveProfileName();

        $this->info("Switched to production environment (https://there-there.app/api) for profile \"{$profileName}\".");
        $this->line('Run `there-there login` to authenticate with production.');

        return self::SUCCESS;
    }
}
