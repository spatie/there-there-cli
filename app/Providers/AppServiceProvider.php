<?php

namespace App\Providers;

use App\Services\CredentialStore;
use App\Services\ThereThereDescriber;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\LaravelConsoleSummary\Contracts\DescriberContract;
use Spatie\OpenApiCli\OpenApiCli;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(DescriberContract::class, ThereThereDescriber::class);

        $specPath = env('OPENAPI_SPEC_PATH', 'https://therethere.app/downloads/openapi.yaml');

        OpenApiCli::register(specPath: $specPath)
            ->useOperationIds()
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
}
