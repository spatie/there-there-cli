# There There CLI

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/there-there-cli.svg?style=flat-square)](https://packagist.org/packages/spatie/there-there-cli)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/there-there-cli.svg?style=flat-square)](https://packagist.org/packages/spatie/there-there-cli)

A command-line tool for [There There](https://there-there.app), interact with the There There API from your terminal.

Full documentation is available at [there-there.app](https://there-there.app).

## Installation

```bash
composer global require spatie/there-there-cli
```

Make sure Composer's global bin directory is in your `PATH`. You can find the path with:

```bash
composer global config bin-dir --absolute
```

## Updating

```bash
composer global require spatie/there-there-cli
```

## Usage

### Authentication

```bash
# Log in (automatically creates a profile named after your workspace)
there-there login

# Log in with a specific profile name
there-there login --profile=spatie

# Log out the active profile
there-there logout

# Log out a specific profile
there-there logout --profile=spatie
```

Get your API token at [there-there.app](https://there-there.app).

### Profiles

If you have multiple workspaces, you can store credentials for each one in a separate profile.

```bash
# Log in to your first workspace
there-there login --profile=spatie

# Log in to your second workspace
there-there login --profile=ohdear

# List all profiles
there-there profiles

# Switch the default profile
there-there use spatie

# Run a single command against a different profile
there-there list-tickets --profile=ohdear
```

### Commands

Every There There API endpoint has a corresponding command. Run `there-there <command> --help` for details on a specific command.

```bash
there-there list-tickets
there-there list-tickets --q="refund request"
there-there list-tickets --filter-search="billing"
there-there list-tickets --filter-created-after=2026-01-01
there-there show-ticket --ticket=TICKET_ULID
there-there reply-to-ticket --ticket=TICKET_ULID --field body="Your reply here"
there-there forward-ticket --ticket=TICKET_ULID --field to_recipients='["email@example.com"]'
there-there add-note-to-ticket --ticket=TICKET_ULID --field body="Internal note"

there-there update-ticket-status --ticket=TICKET_ULID --field status=closed
there-there update-ticket-assignee --ticket=TICKET_ULID --field assignee_ulid=USER_ULID
there-there update-ticket-team --ticket=TICKET_ULID --field team_ulid=TEAM_ULID

there-there add-tag-to-ticket --ticket=TICKET_ULID --tag=TAG_ULID
there-there remove-tag-from-ticket --ticket=TICKET_ULID --tag=TAG_ULID

there-there list-ticket-activities --ticket=TICKET_ULID

there-there list-contacts
there-there show-contact --contact=CONTACT_ULID

there-there list-channels
there-there list-members
there-there list-tags
there-there get-me
```

## Agent Skill

This repository includes an [agent skill](https://skills.sh) that teaches coding agents how to use the There There CLI.

### Install

```bash
there-there install-skill
```

## Testing

```bash
composer test
```

## Releasing a new version

1. **Build the PHAR**:

    ```bash
    php there-there app:build there-there --build-version=1.x.x
    ```

2. **Commit and push**:

    ```bash
    git add builds/there-there
    git commit -m "Release v1.x.x"
    git push origin main
    ```

3. **Create a release** in the GitHub UI.

Users update with `composer global require spatie/there-there-cli`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Spatie](https://github.com/spatie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
