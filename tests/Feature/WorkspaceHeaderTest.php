<?php

use App\Providers\AppServiceProvider;
use App\Services\CredentialStore;
use GuzzleHttp\Psr7\Request;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir().'/there-there-test-'.uniqid();
    mkdir($this->tempDir, 0755, true);

    $this->configPath = $this->tempDir.'/config.json';

    $this->store = new CredentialStore;
    $reflection = new ReflectionClass($this->store);
    $prop = $reflection->getProperty('configPath');
    $prop->setValue($this->store, $this->configPath);

    $this->app->instance(CredentialStore::class, $this->store);

    $this->provider = new AppServiceProvider($this->app);
});

afterEach(function () {
    if (file_exists($this->configPath)) {
        unlink($this->configPath);
    }
    if (is_dir($this->tempDir)) {
        rmdir($this->tempDir);
    }
});

it('attaches X-Workspace-Id when a workspace id is stored', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setWorkspaceId(42);

    $request = new Request('GET', 'https://there-there.app/api/tickets');

    $result = $this->provider->workspaceHeaderMiddleware($request);

    expect($result->getHeaderLine('X-Workspace-Id'))->toBe('42');
});

it('does not attach X-Workspace-Id when no workspace id is stored', function () {
    $request = new Request('GET', 'https://there-there.app/api/me');

    $result = $this->provider->workspaceHeaderMiddleware($request);

    expect($result->hasHeader('X-Workspace-Id'))->toBeFalse();
});

it('does not mutate the original request when adding the header', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setWorkspaceId(7);

    $request = new Request('GET', 'https://there-there.app/api/tickets');

    $result = $this->provider->workspaceHeaderMiddleware($request);

    expect($request->hasHeader('X-Workspace-Id'))->toBeFalse();
    expect($result->getHeaderLine('X-Workspace-Id'))->toBe('7');
});
