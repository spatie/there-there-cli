<?php

namespace App\Commands;

use App\Concerns\RendersBanner;
use App\Services\CredentialStore;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class LoginCommand extends Command
{
    use RendersBanner;

    protected $signature = 'login {--profile= : Profile name to store credentials under}';

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
            $response = Http::withToken($token)
                ->accept('application/json')
                ->withOptions(['allow_redirects' => true, 'verify' => false])
                ->get("{$baseUrl}/me");
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error('Could not connect to There There. Please check your internet connection.');

            return self::FAILURE;
        }

        if (! $response->successful()) {
            $this->error('Invalid API token.');

            return self::FAILURE;
        }

        $name = $response->json('data.user.name', 'unknown');
        $workspace = $response->json('data.workspace.name', 'unknown');
        $workspaceId = $response->json('data.workspace.id');

        $profileName = $this->option('profile') ?? Str::slug($workspace);

        $credentials->setActiveProfile($profileName);
        $credentials->setToken($token);
        $credentials->setUser($name, $workspace);

        if ($workspaceId !== null) {
            $credentials->setWorkspaceId((int) $workspaceId);
        }

        $credentials->setDefaultProfile($profileName);

        $this->newLine();
        $this->info("  Successfully logged in as {$name} ({$workspace})  ");
        $this->line("  Profile: <comment>{$profileName}</comment>");

        return self::SUCCESS;
    }
}
