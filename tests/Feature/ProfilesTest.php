<?php

use App\Services\CredentialStore;

it('can list profiles', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('listProfiles')->once()->andReturn([
        'spatie' => [
            'token' => 'abc',
            'user_name' => 'Freek',
            'workspace_name' => 'Spatie',
            'base_url' => 'https://there-there.app/api',
        ],
        'ohdear' => [
            'token' => 'def',
            'user_name' => 'Freek',
            'workspace_name' => 'Oh Dear',
            'base_url' => 'https://there-there.app/api',
        ],
    ]);
    $credentials->shouldReceive('getDefaultProfileName')->once()->andReturn('spatie');

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('profiles')
        ->assertExitCode(0);
});

it('shows a message when no profiles exist', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('listProfiles')->once()->andReturn([]);

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('profiles')
        ->expectsOutput('No profiles configured. Run `there-there login` to get started.')
        ->assertExitCode(0);
});

it('can switch the default profile', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('profileExists')->with('ohdear')->once()->andReturn(true);
    $credentials->shouldReceive('setDefaultProfile')->with('ohdear')->once();
    $credentials->shouldReceive('setActiveProfile')->with('ohdear')->once();
    $credentials->shouldReceive('getWorkspaceName')->once()->andReturn('Oh Dear');

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('use', ['profile' => 'ohdear'])
        ->expectsOutput('Switched to profile "ohdear" (Oh Dear).')
        ->assertExitCode(0);
});

it('cannot switch to a nonexistent profile', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('profileExists')->with('nonexistent')->once()->andReturn(false);

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('use', ['profile' => 'nonexistent'])
        ->expectsOutput('Profile "nonexistent" does not exist.')
        ->assertExitCode(1);
});

it('can logout a specific profile', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('setActiveProfile')->with('spatie')->once();
    $credentials->shouldReceive('getActiveProfileName')->once()->andReturn('spatie');
    $credentials->shouldReceive('profileExists')->with('spatie')->once()->andReturn(true);
    $credentials->shouldReceive('flush')->once();

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('logout', ['--profile' => 'spatie'])
        ->expectsOutput('Profile "spatie" removed.')
        ->assertExitCode(0);
});

it('can logout all profiles', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('flushAll')->once();

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('logout', ['--all' => true])
        ->expectsOutput('All profiles removed.')
        ->assertExitCode(0);
});

it('cannot logout a nonexistent profile', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('setActiveProfile')->with('nonexistent')->once();
    $credentials->shouldReceive('getActiveProfileName')->once()->andReturn('nonexistent');
    $credentials->shouldReceive('profileExists')->with('nonexistent')->once()->andReturn(false);

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('logout', ['--profile' => 'nonexistent'])
        ->expectsOutput('Profile "nonexistent" does not exist.')
        ->assertExitCode(1);
});
