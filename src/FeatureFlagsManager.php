<?php

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use JetBrains\PhpStorm\Pure;
use Worksome\FeatureFlags\Providers\FakeProvider;
use Worksome\FeatureFlags\Providers\LaunchDarkly\LaunchDarklyProvider;

class FeatureFlagsManager extends Manager
{
    #[Pure]
    public function createLaunchDarklyDriver(): LaunchDarklyProvider
    {
        return new LaunchDarklyProvider(
            $this->container->get(FeatureFlagsOverridesRepository::class),
        );
    }

    #[Pure]
    public function createFakeDriver(): FakeProvider
    {
        return new FakeProvider(
            $this->container->get(FeatureFlagsOverridesRepository::class),
        );
    }

    #[Pure]
    public function getDefaultDriver(): string
    {
        return strval($this->config->get('feature-flags.default'));
    }
}
