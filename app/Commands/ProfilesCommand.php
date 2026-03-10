<?php

namespace App\Commands;

use App\Services\CredentialStore;
use LaravelZero\Framework\Commands\Command;

class ProfilesCommand extends Command
{
    protected $signature = 'profiles';

    protected $description = 'List all configured profiles';

    public function handle(CredentialStore $credentials): int
    {
        $profiles = $credentials->listProfiles();

        if (empty($profiles)) {
            $this->line('No profiles configured. Run `there-there login` to get started.');

            return self::SUCCESS;
        }

        $defaultProfile = $credentials->getDefaultProfileName();

        $rows = [];

        foreach ($profiles as $name => $data) {
            $rows[] = [
                $name === $defaultProfile ? "* {$name}" : "  {$name}",
                $data['user_name'] ?? '',
                $data['workspace_name'] ?? '',
                $data['base_url'] ?? 'https://there-there.app/api',
            ];
        }

        $this->table(
            ['Profile', 'User', 'Workspace', 'Base URL'],
            $rows,
        );

        return self::SUCCESS;
    }
}
