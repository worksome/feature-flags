<?php

declare(strict_types=1);

use Worksome\FeatureFlags\Providers\Api\FakeApiProvider;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;

it('should throw exception if access token is missing', function () {
    $this->fakeProvider = new FakeApiProvider(false);
})->throws(LaunchDarklyMissingAccessTokenException::class);

it('should return true for isAccessTokenValid', function () {
    $this->fakeProvider = new FakeApiProvider();
    expect($this->fakeProvider->isAccessTokenValid())->toBetruthy();
});
