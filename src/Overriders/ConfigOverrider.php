<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Overriders;

use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Illuminate\Support\Facades\Config;
use Worksome\FeatureFlags\Contracts\FeatureFlagOverrider;

class ConfigOverrider implements FeatureFlagOverrider
{
    /**
     * Note: a flag key with null as value is considered not present, will return false
     */
    public function has(FeatureFlagEnum $key): bool
    {
        return Config::has(sprintf('feature-flags.overrides.%s', $key->value))
               && Config::get(sprintf('feature-flags.overrides.%s', $key->value)) !== null;
    }

    public function get(FeatureFlagEnum $key): bool
    {
        return (bool) Config::get(sprintf('feature-flags.overrides.%s', $key->value));
    }

    /**
     * Note: null value is considered not present, will return false
     */
    public function hasAll(): bool
    {
        return Config::has('feature-flags.override-all')
               && Config::get('feature-flags.override-all') !== null;
    }

    public function getAll(): bool
    {
        return (bool) Config::get('feature-flags.override-all');
    }
}
