<?php

use App\Services\CredentialStore;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir().'/there-there-test-'.uniqid();
    mkdir($this->tempDir, 0755, true);

    $this->configPath = $this->tempDir.'/config.json';
});

afterEach(function () {
    if (file_exists($this->configPath)) {
        unlink($this->configPath);
    }
    if (is_dir($this->tempDir)) {
        rmdir($this->tempDir);
    }
});

function createStore(string $configPath): CredentialStore
{
    $store = new CredentialStore;
    $reflection = new ReflectionClass($store);
    $prop = $reflection->getProperty('configPath');
    $prop->setValue($store, $configPath);

    return $store;
}

it('returns null token when no config exists', function () {
    $store = createStore($this->configPath);

    expect($store->getToken())->toBeNull();
});

it('stores and retrieves a token in a profile', function () {
    $store = createStore($this->configPath);
    $store->setActiveProfile('spatie');
    $store->setToken('my-token');

    expect($store->getToken())->toBe('my-token');
});

it('stores multiple profiles independently', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setToken('spatie-token');
    $store->setUser('Freek', 'Spatie');

    $store->setActiveProfile('ohdear');
    $store->setToken('ohdear-token');
    $store->setUser('Freek', 'Oh Dear');

    $store->setActiveProfile('spatie');
    expect($store->getToken())->toBe('spatie-token');
    expect($store->getWorkspaceName())->toBe('Spatie');

    $store->setActiveProfile('ohdear');
    expect($store->getToken())->toBe('ohdear-token');
    expect($store->getWorkspaceName())->toBe('Oh Dear');
});

it('lists all profiles', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setToken('spatie-token');

    $store->setActiveProfile('ohdear');
    $store->setToken('ohdear-token');

    $profiles = $store->listProfiles();

    expect($profiles)->toHaveCount(2);
    expect(array_keys($profiles))->toBe(['spatie', 'ohdear']);
});

it('can set and get the default profile', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setToken('token');
    $store->setDefaultProfile('spatie');

    expect($store->getDefaultProfileName())->toBe('spatie');
});

it('checks if a profile exists', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setToken('token');

    expect($store->profileExists('spatie'))->toBeTrue();
    expect($store->profileExists('nonexistent'))->toBeFalse();
});

it('flushes only the active profile', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setToken('spatie-token');
    $store->setDefaultProfile('spatie');

    $store->setActiveProfile('ohdear');
    $store->setToken('ohdear-token');

    $store->setActiveProfile('spatie');
    $store->flush();

    expect($store->profileExists('spatie'))->toBeFalse();
    expect($store->profileExists('ohdear'))->toBeTrue();
});

it('switches default profile when active default is flushed', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setToken('spatie-token');
    $store->setDefaultProfile('spatie');

    $store->setActiveProfile('ohdear');
    $store->setToken('ohdear-token');

    $store->setActiveProfile('spatie');
    $store->flush();

    expect($store->getDefaultProfileName())->toBe('ohdear');
});

it('flushes all profiles', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setToken('spatie-token');

    $store->setActiveProfile('ohdear');
    $store->setToken('ohdear-token');

    $store->flushAll();

    expect($store->listProfiles())->toBeEmpty();
});

it('uses default base url when profile has none', function () {
    $store = createStore($this->configPath);

    expect($store->getBaseUrl())->toBe('https://there-there.app/api');
});

it('stores base url per profile', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('local');
    $store->setBaseUrl('https://there-there.test/api');

    $store->setActiveProfile('prod');
    $store->setBaseUrl('https://there-there.app/api');

    $store->setActiveProfile('local');
    expect($store->getBaseUrl())->toBe('https://there-there.test/api');

    $store->setActiveProfile('prod');
    expect($store->getBaseUrl())->toBe('https://there-there.app/api');
});

it('returns null workspace id when not set', function () {
    $store = createStore($this->configPath);

    expect($store->getWorkspaceId())->toBeNull();
});

it('stores and retrieves a workspace id', function () {
    $store = createStore($this->configPath);
    $store->setActiveProfile('spatie');
    $store->setWorkspaceId(42);

    expect($store->getWorkspaceId())->toBe(42);
});

it('stores workspace ids independently per profile', function () {
    $store = createStore($this->configPath);

    $store->setActiveProfile('spatie');
    $store->setWorkspaceId(1);

    $store->setActiveProfile('ohdear');
    $store->setWorkspaceId(2);

    $store->setActiveProfile('spatie');
    expect($store->getWorkspaceId())->toBe(1);

    $store->setActiveProfile('ohdear');
    expect($store->getWorkspaceId())->toBe(2);
});
