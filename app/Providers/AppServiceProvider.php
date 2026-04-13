<?php

namespace App\Providers;

use App\Services\CredentialStore;
use App\Services\ThereThereDescriber;
use App\Support\LocalHostDetector;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\LaravelConsoleSummary\Contracts\DescriberContract;
use Psr\Http\Message\RequestInterface;
use Spatie\OpenApiCli\OpenApiCli;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(DescriberContract::class, ThereThereDescriber::class);

        Http::globalRequestMiddleware($this->workspaceHeaderMiddleware(...));

        Http::globalOptions(function () {
            $baseUrl = app(CredentialStore::class)->getBaseUrl();

            if (LocalHostDetector::isLocal($baseUrl)) {
                return ['verify' => false];
            }

            return [];
        });

        OpenApiCli::register(specPath: base_path('resources/openapi.yaml'))
            ->useOperationIds()
            ->baseUrl(app(CredentialStore::class)->getBaseUrl())
            ->cache(ttl: 60 * 60 * 24)
            ->auth(fn () => app(CredentialStore::class)->getToken())
            ->onError(function (Response $response, Command $command) {
                if ($response->status() === 401) {
                    $command->error(
                        'Your API token is invalid or expired. Run `there-there login` to authenticate.',
                    );

                    return true;
                }

                return false;
            });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CredentialStore::class);
    }

    public function workspaceHeaderMiddleware(RequestInterface $request): RequestInterface
    {
        $workspaceId = app(CredentialStore::class)->getWorkspaceId();

        if ($workspaceId === null) {
            return $request;
        }

        return $request->withHeader('X-Workspace-Id', (string) $workspaceId);
    }
}
