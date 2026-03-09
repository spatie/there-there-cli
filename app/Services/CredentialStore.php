<?php

namespace App\Services;

class CredentialStore
{
    private string $configPath;

    public function __construct()
    {
        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? '';

        $this->configPath = "{$home}/.there-there/config.json";
    }

    public function getToken(): ?string
    {
        return $this->readConfig()['token'] ?? null;
    }

    public function setToken(string $token): void
    {
        $this->set('token', $token);
    }

    public function getBaseUrl(): string
    {
        return $this->readConfig()['base_url'] ?? 'https://there-there.app/api';
    }

    public function setBaseUrl(string $url): void
    {
        $this->set('base_url', $url);
    }

    private function set(string $key, string $value): void
    {
        $this->ensureConfigDirectoryExists();

        $data = $this->readConfig();
        $data[$key] = $value;

        file_put_contents(
            $this->configPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        );
    }

    public function flush(): void
    {
        $this->ensureConfigDirectoryExists();

        file_put_contents(
            $this->configPath,
            json_encode((object) [], JSON_PRETTY_PRINT),
        );
    }

    private function ensureConfigDirectoryExists(): void
    {
        $directory = dirname($this->configPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /** @return array<string, mixed> */
    private function readConfig(): array
    {
        if (! file_exists($this->configPath)) {
            return [];
        }

        return json_decode(file_get_contents($this->configPath), true) ?? [];
    }
}
