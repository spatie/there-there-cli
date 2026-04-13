<?php

namespace App\Services;

class CredentialStore
{
    private string $configPath;

    private ?string $activeProfile = null;

    public function __construct(?string $configPath = null)
    {
        if ($configPath !== null) {
            $this->configPath = $configPath;
        } else {
            $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? '';
            $this->configPath = "{$home}/.there-there/config.json";
        }

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

    public function getWorkspaceId(): ?int
    {
        $id = $this->getProfileData()['workspace_id'] ?? null;

        return $id !== null ? (int) $id : null;
    }

    public function setWorkspaceId(int $workspaceId): void
    {
        $this->mergeProfileValues(['workspace_id' => $workspaceId]);
    }

    public function setUser(string $name, string $workspace): void
    {
        $this->mergeProfileValues([
            'user_name' => $name,
            'workspace_name' => $workspace,
        ]);
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

        if (($data['default_profile'] ?? null) === $profileName) {
            $remaining = array_keys($data['profiles'] ?? []);
            $data['default_profile'] = $remaining[0] ?? 'default';
        }

        $this->writeConfig($data);
    }

    public function flushAll(): void
    {
        $this->writeConfig([]);
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
        $this->mergeProfileValues([$key => $value]);
    }

    /** @param  array<string, mixed>  $values */
    private function mergeProfileValues(array $values): void
    {
        $profileName = $this->getActiveProfileName();

        $data = $this->readConfig();
        $data['profiles'][$profileName] = array_merge(
            $data['profiles'][$profileName] ?? [],
            $values,
        );

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

        @chmod($this->configPath, 0600);
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
