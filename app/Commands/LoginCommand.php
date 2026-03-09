<?php

namespace App\Commands;

use App\Concerns\RendersBanner;
use App\Services\CredentialStore;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;

class LoginCommand extends Command
{
    use RendersBanner;

    protected $signature = 'login';

    protected $description = 'Store your There There API token for authentication';

    public function handle(CredentialStore $credentials): int
    {
        $this->renderBanner($this->output);

        $baseUrl = $credentials->getBaseUrl();
        $appUrl = preg_replace('#/api$#', '', $baseUrl);

        $tokenUrl = "{$appUrl}/app/settings/user/api-tokens";
        $this->line("You can generate a token from your workspace settings at <href={$tokenUrl}>{$tokenUrl}</>");
        $this->newLine();

        $token = $this->secret('Enter your API token');

        if (! $token) {
            $this->error('No token provided.');

            return self::FAILURE;
        }

        try {
            $response = Http::withToken($token)->get("{$baseUrl}/me");
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error('Could not connect to There There. Please check your internet connection.');

            return self::FAILURE;
        }

        if (! $response->successful()) {
            $this->error('Invalid API token.');

            return self::FAILURE;
        }

        $credentials->setToken($token);

        $name = $response->json('user.name', 'unknown');
        $workspace = $response->json('workspace.name', 'unknown');

        $this->newLine();
        $this->info("  Successfully logged in as {$name} ({$workspace})  ");

        return self::SUCCESS;
    }
}
