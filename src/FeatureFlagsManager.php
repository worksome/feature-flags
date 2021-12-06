<?php

namespace Worksome\FeatureFlags;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Manager;
use JetBrains\PhpStorm\Pure;
use Worksome\FeatureFlags\LaunchDarkly\LaunchDarklyProvider;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider as FeatureFlagsProviderContract;

class FeatureFlagsManager extends Manager
{
    #[Pure]
    public function createLaunchDarklyDriver(): LaunchDarklyProvider
    {
        return new LaunchDarklyProvider;
    }

    #[Pure]
    public function getDefaultDriver(): string
    {
        return Config::get('feature-flags.default');
    }

    public function provider(string $driver): FeatureFlagsProviderContract
    {
        return $this->driver($driver);
    }
}
