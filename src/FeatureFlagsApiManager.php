<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use JetBrains\PhpStorm\Pure;
use Worksome\FeatureFlags\Providers\Api\FakeApiProvider;
use Worksome\FeatureFlags\Providers\LaunchDarkly\Api\LaunchDarklyApiProvider;

class FeatureFlagsApiManager extends Manager
{
    #[Pure]
    public function createLaunchDarklyDriver(): LaunchDarklyApiProvider
    {
        return new LaunchDarklyApiProvider();
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
