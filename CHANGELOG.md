# Changelog

All notable changes to `there-there-cli` will be documented in this file. Earlier releases are available on the [GitHub releases page](https://github.com/spatie/there-there-cli/releases).

## 0.8.3 - 2026-11-06

### PHP 8.6 compatibility

- Fix CLI startup on PHP 8.6 by upgrading Laravel Zero and requiring the upstream Illuminate Container bootstrap fix.
- Rebuild the bundled `there-there` executable while retaining PHP 8.4.0 support.
- Verify all 39 tests (89 assertions) and distributed PHAR startup, command registration, help and profile listing on PHP 8.4, 8.5 and 8.6 across Linux and Windows. Actual PHP 8.4.0 source and PHAR execution was also verified locally.

Update with `composer global require spatie/there-there-cli`.

The tested PHAR is committed at `builds/there-there` in this release (SHA-256: `4c03eb3ddcfbfdd05e052cd2b812120b31f9c1b29c70f0afe70a8538cae53b67`). No authenticated live-workspace compatibility claim is made by these offline checks.

**Full changelog:** https://github.com/spatie/there-there-cli/compare/0.8.2...0.8.3
