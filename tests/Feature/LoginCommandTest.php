<?php

use App\Services\CredentialStore;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->store = makeTempCredentialStore();
    $this->app->instance(CredentialStore::class, $this->store);
});

it('stores token and workspace id after a successful login', function () {
    Http::fake([
        '*/api/me' => Http::response([
            'data' => [
                'user' => [
                    'id' => 5,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                ],
                'workspace' => [
                    'id' => 42,
                    'ulid' => '01hx9f3k2m',
                    'name' => 'Acme Support',
                    'slug' => 'acme-support',
                ],
            ],
        ]),
    ]);

    $this->artisan('login')
        ->expectsQuestion('Enter your API token', 'valid-token')
        ->assertSuccessful();

    expect($this->store->getToken())->toBe('valid-token');
    expect($this->store->getWorkspaceId())->toBe(42);
    expect($this->store->getUserName())->toBe('Jane Smith');
    expect($this->store->getWorkspaceName())->toBe('Acme Support');
});

it('fails when the token is invalid', function () {
    Http::fake([
        '*/api/me' => Http::response(['message' => 'Unauthenticated.'], 401),
    ]);

    $this->artisan('login')
        ->expectsQuestion('Enter your API token', 'bad-token')
        ->assertFailed();

    expect($this->store->getToken())->toBeNull();
});

it('fails when no token is provided', function () {
    $this->artisan('login')
        ->expectsQuestion('Enter your API token', '')
        ->assertFailed();

    expect($this->store->getToken())->toBeNull();
});

it('slugifies the workspace name as the default profile', function () {
    Http::fake([
        '*/api/me' => Http::response([
            'data' => [
                'user' => ['id' => 1, 'name' => 'Jane'],
                'workspace' => ['id' => 7, 'name' => 'Acme Support'],
            ],
        ]),
    ]);

    $this->artisan('login')
        ->expectsQuestion('Enter your API token', 'token')
        ->assertSuccessful();

    expect($this->store->profileExists('acme-support'))->toBeTrue();
});

it('uses the provided profile option name', function () {
    Http::fake([
        '*/api/me' => Http::response([
            'data' => [
                'user' => ['id' => 1, 'name' => 'Jane'],
                'workspace' => ['id' => 7, 'name' => 'Acme Support'],
            ],
        ]),
    ]);

    $this->artisan('login --profile=custom')
        ->expectsQuestion('Enter your API token', 'token')
        ->assertSuccessful();

    expect($this->store->profileExists('custom'))->toBeTrue();
    expect($this->store->profileExists('acme-support'))->toBeFalse();
});
