<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use JetBrains\PhpStorm\Pure;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;
use Worksome\FeatureFlags\Providers\Api\FakeApiProvider;
use Worksome\FeatureFlags\Providers\LaunchDarkly\Api\LaunchDarklyApiProvider;

class FeatureFlagsApiManager extends Manager
{
    #[Pure]
    public function createLaunchDarklyDriver(): LaunchDarklyApiProvider
    {
        $token = $this->config->get('feature-flags.providers.launchdarkly.access-token');
        if (! is_string($token)) {
            throw new LaunchDarklyMissingAccessTokenException();
        }

        return new LaunchDarklyApiProvider(
            accessToken: $token,
        );
    }

    #[Pure]
    public function createFakeDriver(): FakeApiProvider
    {
        return new FakeApiProvider();
    }

    #[Pure]
    public function getDefaultDriver(): string
    {
        return strval($this->config->get('feature-flags.default'));
    }
}
