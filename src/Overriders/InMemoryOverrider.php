<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Overriders;

use Illuminate\Support\Arr;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagOverrider;

class InMemoryOverrider implements FeatureFlagOverrider
{
    /**
     * @var array<string, bool|null> $overrides
     */
    private array $overrides = [];

    /**
     * @var bool|null $overrideAll
     */
    private bool|null $overrideAll = null;

    /**
     * Note: a flag key with null as value is considered not present, will return false
     */
    public function has(FeatureFlagEnum $key): bool
    {
        return Arr::has($this->overrides, $key->value)
            && Arr::get($this->overrides, $key->value) !== null;
    }

    public function get(FeatureFlagEnum $key): bool
    {
        return (bool) Arr::get($this->overrides, $key->value, false);
    }

    /**
     * Note: null value is considered not present, will return false
     */
    public function hasAll(): bool
    {
        return $this->overrideAll !== null;
    }

    public function getAll(): bool
    {
        return (bool) $this->overrideAll;
    }

    public function setAll(bool|null $value = null): static
    {
        $this->overrideAll = $value;

        return $this;
    }

    public function set(FeatureFlagEnum $key, mixed $value): static
    {
        Arr::set($this->overrides, $key->value, $value); // @phpstan-ignore assign.propertyType

        return $this;
    }

    /** @param array<string, bool|null>|null $overriders */
    public function overrides(array|null $overriders): array|self
    {
        if ($overriders) {
            $this->overrides = $overriders;

            return $this;
        }

        return $this->overrides;
    }
}
