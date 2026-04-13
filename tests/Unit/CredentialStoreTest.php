<?php

beforeEach(function () {
    $this->store = makeTempCredentialStore();
});

it('returns null token when no config exists', function () {
    expect($this->store->getToken())->toBeNull();
});

it('stores and retrieves a token in a profile', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('my-token');

    expect($this->store->getToken())->toBe('my-token');
});

it('stores multiple profiles independently', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('spatie-token');
    $this->store->setUser('Freek', 'Spatie');

    $this->store->setActiveProfile('ohdear');
    $this->store->setToken('ohdear-token');
    $this->store->setUser('Freek', 'Oh Dear');

    $this->store->setActiveProfile('spatie');
    expect($this->store->getToken())->toBe('spatie-token');
    expect($this->store->getWorkspaceName())->toBe('Spatie');

    $this->store->setActiveProfile('ohdear');
    expect($this->store->getToken())->toBe('ohdear-token');
    expect($this->store->getWorkspaceName())->toBe('Oh Dear');
});

it('lists all profiles', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('spatie-token');

    $this->store->setActiveProfile('ohdear');
    $this->store->setToken('ohdear-token');

    $profiles = $this->store->listProfiles();

    expect($profiles)->toHaveCount(2);
    expect(array_keys($profiles))->toBe(['spatie', 'ohdear']);
});

it('can set and get the default profile', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('token');
    $this->store->setDefaultProfile('spatie');

    expect($this->store->getDefaultProfileName())->toBe('spatie');
});

it('checks if a profile exists', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('token');

    expect($this->store->profileExists('spatie'))->toBeTrue();
    expect($this->store->profileExists('nonexistent'))->toBeFalse();
});

it('flushes only the active profile', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('spatie-token');
    $this->store->setDefaultProfile('spatie');

    $this->store->setActiveProfile('ohdear');
    $this->store->setToken('ohdear-token');

    $this->store->setActiveProfile('spatie');
    $this->store->flush();

    expect($this->store->profileExists('spatie'))->toBeFalse();
    expect($this->store->profileExists('ohdear'))->toBeTrue();
});

it('switches default profile when active default is flushed', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('spatie-token');
    $this->store->setDefaultProfile('spatie');

    $this->store->setActiveProfile('ohdear');
    $this->store->setToken('ohdear-token');

    $this->store->setActiveProfile('spatie');
    $this->store->flush();

    expect($this->store->getDefaultProfileName())->toBe('ohdear');
});

it('flushes all profiles', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setToken('spatie-token');

    $this->store->setActiveProfile('ohdear');
    $this->store->setToken('ohdear-token');

    $this->store->flushAll();

    expect($this->store->listProfiles())->toBeEmpty();
});

it('uses default base url when profile has none', function () {
    expect($this->store->getBaseUrl())->toBe('https://there-there.app/api');
});

it('stores base url per profile', function () {
    $this->store->setActiveProfile('local');
    $this->store->setBaseUrl('https://there-there.test/api');

    $this->store->setActiveProfile('prod');
    $this->store->setBaseUrl('https://there-there.app/api');

    $this->store->setActiveProfile('local');
    expect($this->store->getBaseUrl())->toBe('https://there-there.test/api');

    $this->store->setActiveProfile('prod');
    expect($this->store->getBaseUrl())->toBe('https://there-there.app/api');
});

it('returns null workspace id when not set', function () {
    expect($this->store->getWorkspaceId())->toBeNull();
});

it('stores and retrieves a workspace id', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setWorkspaceId(42);

    expect($this->store->getWorkspaceId())->toBe(42);
});

it('stores workspace ids independently per profile', function () {
    $this->store->setActiveProfile('spatie');
    $this->store->setWorkspaceId(1);

    $this->store->setActiveProfile('ohdear');
    $this->store->setWorkspaceId(2);

    $this->store->setActiveProfile('spatie');
    expect($this->store->getWorkspaceId())->toBe(1);

    $this->store->setActiveProfile('ohdear');
    expect($this->store->getWorkspaceId())->toBe(2);
});
