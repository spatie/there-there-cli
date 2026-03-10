<?php

use App\Services\CredentialStore;

it('can logout and clear credentials for the active profile', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('getActiveProfileName')->once()->andReturn('default');
    $credentials->shouldReceive('profileExists')->with('default')->once()->andReturn(true);
    $credentials->shouldReceive('flush')->once();

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('logout')
        ->expectsOutput('Profile "default" removed.')
        ->assertExitCode(0);
});
