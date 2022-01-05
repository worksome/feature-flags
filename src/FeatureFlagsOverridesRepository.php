<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Facades\Config;

class FeatureFlagsOverridesRepository
{
    public function has(string $key): bool
    {
        return $this->hasAll()
            || Config::has(sprintf('feature-flags.overrides.%s', $key));
    }

    public function get(string $key): bool
    {
        if ($this->hasAll()) {
            return $this->getAll();
        }

        return (bool) Config::get(sprintf('feature-flags.overrides.%s', $key));
    }

    public function hasAll(): bool
    {
        return Config::get('feature-flags.override-all') !== null;
    }

    public function getAll(): bool
    {
        return (bool) Config::get('feature-flags.override-all');
    }
}
