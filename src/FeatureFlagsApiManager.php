<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;
use Worksome\FeatureFlags\Providers\Api\FakeApiProvider;
use Worksome\FeatureFlags\Providers\LaunchDarkly\Api\LaunchDarklyApiProvider;

class FeatureFlagsApiManager extends Manager
{
    public function createLaunchDarklyDriver(): LaunchDarklyApiProvider
    {
        $token = $this->config->get('feature-flags.providers.launchdarkly.access-token');

        assert(is_string($token), new LaunchDarklyMissingAccessTokenException());

        return new LaunchDarklyApiProvider(
            accessToken: $token,
        );
    }

    public function createFakeDriver(): FakeApiProvider
    {
        return new FakeApiProvider();
    }

    public function getDefaultDriver(): string
    {
        return strval($this->config->get('feature-flags.default')); // @phpstan-ignore-line
    }
}
