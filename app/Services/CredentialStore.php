<?php

namespace App\Services;

class CredentialStore
{
    private string $configPath;

    private ?string $activeProfile = null;

    public function __construct()
    {
        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? '';

        $this->configPath = "{$home}/.there-there/config.json";

        $this->activeProfile = $this->resolveProfileFromArgv();
    }

    public function getToken(): ?string
    {
        return $this->getProfileData()['token'] ?? null;
    }

    public function setToken(string $token): void
    {
        $this->setProfileValue('token', $token);
    }

    public function getBaseUrl(): string
    {
        return $this->getProfileData()['base_url'] ?? 'https://there-there.app/api';
    }

    public function setBaseUrl(string $url): void
    {
        $this->setProfileValue('base_url', $url);
    }

    public function getUserName(): ?string
    {
        return $this->getProfileData()['user_name'] ?? null;
    }

    public function getWorkspaceName(): ?string
    {
        return $this->getProfileData()['workspace_name'] ?? null;
    }

    public function setUser(string $name, string $workspace): void
    {
        $profileName = $this->getActiveProfileName();

        $data = $this->readConfig();
        $data['profiles'][$profileName]['user_name'] = $name;
        $data['profiles'][$profileName]['workspace_name'] = $workspace;

        $this->writeConfig($data);
    }

    public function getActiveProfileName(): string
    {
        if ($this->activeProfile) {
            return $this->activeProfile;
        }

        $config = $this->readConfig();

        return $config['default_profile'] ?? 'default';
    }

    public function setActiveProfile(string $profile): void
    {
        $this->activeProfile = $profile;
    }

    public function getDefaultProfileName(): string
    {
        $config = $this->readConfig();

        return $config['default_profile'] ?? 'default';
    }

    public function setDefaultProfile(string $profile): void
    {
        $data = $this->readConfig();
        $data['default_profile'] = $profile;

        $this->writeConfig($data);
    }

    /** @return array<string, array<string, mixed>> */
    public function listProfiles(): array
    {
        $config = $this->readConfig();

        return $config['profiles'] ?? [];
    }

    public function profileExists(string $profile): bool
    {
        $config = $this->readConfig();

        return isset($config['profiles'][$profile]);
    }

    public function flush(): void
    {
        $profileName = $this->getActiveProfileName();

        $data = $this->readConfig();
        unset($data['profiles'][$profileName]);

        if ($data['default_profile'] === $profileName) {
            $remaining = array_keys($data['profiles'] ?? []);
            $data['default_profile'] = $remaining[0] ?? 'default';
        }

        $this->writeConfig($data);
    }

    public function flushAll(): void
    {
        $this->ensureConfigDirectoryExists();

        file_put_contents(
            $this->configPath,
            json_encode((object) [], JSON_PRETTY_PRINT),
        );
    }

    private function resolveProfileFromArgv(): ?string
    {
        $argv = $_SERVER['argv'] ?? [];

        foreach ($argv as $i => $arg) {
            if (str_starts_with($arg, '--profile=')) {
                return substr($arg, 10);
            }

            if ($arg === '--profile' && isset($argv[$i + 1])) {
                return $argv[$i + 1];
            }
        }

        return null;
    }

    /** @return array<string, mixed> */
    private function getProfileData(): array
    {
        $config = $this->readConfig();
        $profileName = $this->getActiveProfileName();

        return $config['profiles'][$profileName] ?? [];
    }

    private function setProfileValue(string $key, string $value): void
    {
        $profileName = $this->getActiveProfileName();

        $data = $this->readConfig();
        $data['profiles'][$profileName][$key] = $value;

        if (! isset($data['default_profile'])) {
            $data['default_profile'] = $profileName;
        }

        $this->writeConfig($data);
    }

    private function writeConfig(array $data): void
    {
        $this->ensureConfigDirectoryExists();

        file_put_contents(
            $this->configPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
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
