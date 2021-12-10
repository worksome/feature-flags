<?php

declare(strict_types=1);

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
        return new LaunchDarklyProvider();
    }

    #[Pure]
    public function createFakeDriver(): FakeProvider
    {
        return new FakeProvider();
    }

    #[Pure]
    public function getDefaultDriver(): string
    {
        return strval($this->config->get('feature-flags.default'));
    }
}
