<?php

declare(strict_types=1);

use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;
use Worksome\FeatureFlags\Providers\Api\FakeApiProvider;

it('should throw exception if access token is missing', function () {
    $this->fakeProvider = new FakeApiProvider(false);
})->throws(LaunchDarklyMissingAccessTokenException::class);

it('should return true for isAccessTokenValid', function () {
    $this->fakeProvider = new FakeApiProvider();
    expect($this->fakeProvider->isAccessTokenValid())->toBetruthy();
});
