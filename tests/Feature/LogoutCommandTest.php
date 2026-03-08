<?php

use App\Services\CredentialStore;

it('can logout and clear credentials', function () {
    $credentials = mock(CredentialStore::class);
    $credentials->shouldReceive('flush')->once();

    app()->instance(CredentialStore::class, $credentials);

    $this->artisan('logout')
        ->expectsOutput('Logged out successfully.')
        ->assertExitCode(0);
});
