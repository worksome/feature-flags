<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Overriders;

use Illuminate\Contracts\Config\Repository;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagOverrider;

readonly class ConfigOverrider implements FeatureFlagOverrider
{
    public function __construct(
        private Repository $config,
    ) {
    }

    /**
     * Note: a flag key with null as value is considered not present, will return false
     */
    public function has(FeatureFlagEnum $key): bool
    {
        return $this->config->has(sprintf('feature-flags.overriders.config.overrides.%s', $key->value))
            && $this->config->get(sprintf('feature-flags.overriders.config.overrides.%s', $key->value)) !== null;
    }

    public function get(FeatureFlagEnum $key): bool
    {
        return (bool) $this->config->get(sprintf('feature-flags.overriders.config.overrides.%s', $key->value));
    }

    /**
     * Note: null value is considered not present, will return false
     */
    public function hasAll(): bool
    {
        return $this->config->has('feature-flags.overriders.config.override_all')
            && $this->config->get('feature-flags.overriders.config.override_all') !== null;
    }

    public function getAll(): bool
    {
        return (bool) $this->config->get('feature-flags.overriders.config.override_all');
    }

    public function set(FeatureFlagEnum $key, bool|null $value): static
    {
        $this->config->set(sprintf('feature-flags.overriders.config.overrides.%s', $key->value), $value);

        return $this;
    }

    public function setAll(bool|null $value): static
    {
        $this->config->set('feature-flags.overriders.config.override_all', $value);

        return $this;
    }
}
