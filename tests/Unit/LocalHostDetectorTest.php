<?php

use App\Support\LocalHostDetector;

it('flags localhost as local', function () {
    expect(LocalHostDetector::isLocal('http://localhost/api'))->toBeTrue();
});

it('flags 127.0.0.1 as local', function () {
    expect(LocalHostDetector::isLocal('http://127.0.0.1:8000/api'))->toBeTrue();
});

it('flags the IPv6 loopback as local', function () {
    expect(LocalHostDetector::isLocal('http://[::1]/api'))->toBeTrue();
});

it('flags .test domains as local', function () {
    expect(LocalHostDetector::isLocal('https://there-there.test/api'))->toBeTrue();
});

it('treats there-there.app as remote', function () {
    expect(LocalHostDetector::isLocal('https://there-there.app/api'))->toBeFalse();
});

it('treats unrelated domains as remote', function () {
    expect(LocalHostDetector::isLocal('https://example.com/api'))->toBeFalse();
});

it('does not match .test as a substring elsewhere in the host', function () {
    expect(LocalHostDetector::isLocal('https://test.example.com/api'))->toBeFalse();
});

it('returns false for empty or malformed input', function () {
    expect(LocalHostDetector::isLocal(''))->toBeFalse();
    expect(LocalHostDetector::isLocal('not a url'))->toBeFalse();
});
