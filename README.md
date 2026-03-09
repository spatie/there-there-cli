# There There CLI

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/there-there-cli.svg?style=flat-square)](https://packagist.org/packages/spatie/there-there-cli)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/there-there-cli.svg?style=flat-square)](https://packagist.org/packages/spatie/there-there-cli)

A command-line tool for [There There](https://there-there.app), interact with the There There API from your terminal.

Full documentation is available at [there-there.com](https://there-there.com).

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
# Log in with your There There API token
there-there login

# Log out
there-there logout
```

Get your API token at [there-there.app](https://there-there.app).

### Commands

Every There There API endpoint has a corresponding command. Run `there-there <command> --help` for details on a specific command.

```bash
there-there list-tickets
there-there show-ticket --ticket-id=<id>
there-there reply-to-ticket --ticket-id=<id> --field body="Your reply here"
there-there forward-ticket --ticket-id=<id> --field to="email@example.com"
there-there add-note-to-ticket --ticket-id=<id> --field body="Internal note"

there-there update-ticket-status --ticket-id=<id> --field status=closed
there-there update-ticket-assignee --ticket-id=<id> --field member_id=<member-id>
there-there update-ticket-team --ticket-id=<id> --field team_id=<team-id>

there-there add-tag-to-ticket --ticket-id=<id> --field tag_id=<tag-id>
there-there remove-tag-from-ticket --ticket-id=<id> --tag-id=<tag-id>

there-there list-ticket-activities --ticket-id=<id>

there-there list-contacts
there-there show-contact --contact-id=<id>

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
