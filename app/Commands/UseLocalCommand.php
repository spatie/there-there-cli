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
        $credentials->setBaseUrl('http://there-there.test/api');

        $this->info('Switched to local environment (http://there-there.test/api)');
        $this->line('Run `there-there login` to authenticate with your local instance.');

        return self::SUCCESS;
    }
}
