<?php

use App\Services\CredentialStore;
use Tests\TestCase;

uses(TestCase::class)->in('Feature');

uses()->afterEach(function () {
    if (! isset($this->configPath)) {
        return;
    }

    if (file_exists($this->configPath)) {
        unlink($this->configPath);
    }

    $directory = dirname($this->configPath);

    if (! is_dir($directory)) {
        return;
    }

    if (! str_contains($directory, 'there-there-test-')) {
        return;
    }

    rmdir($directory);
})->in('Feature', 'Unit');

function makeTempCredentialStore(): CredentialStore
{
    $tempDir = sys_get_temp_dir().'/there-there-test-'.uniqid();
    mkdir($tempDir, 0755, true);

    $configPath = $tempDir.'/config.json';

    test()->configPath = $configPath;

    return new CredentialStore($configPath);
}
