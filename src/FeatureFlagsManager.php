<?php

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use JetBrains\PhpStorm\Pure;
use Worksome\FeatureFlags\LaunchDarkly\LaunchDarklyProvider;

class FeatureFlagsManager extends Manager
{
    #[Pure]
    public function createLaunchDarklyDriver(): LaunchDarklyProvider
    {
        return new LaunchDarklyProvider();
    }

    #[Pure]
    public function getDefaultDriver(): string
    {
        return strval($this->config->get('feature-flags.default'));
    }
}
