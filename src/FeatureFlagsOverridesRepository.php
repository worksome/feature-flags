<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Facades\Config;

class FeatureFlagsOverridesRepository
{
    public function has(string $key): bool
    {
        return Config::has(sprintf('feature-flags.overrides.%s', $key));
    }

    public function get(string $key): bool
    {
        return (bool) Config::get(sprintf('feature-flags.overrides.%s', $key));
    }
}
