<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Contracts;

use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;

interface FeatureFlagOverrider
{
    public function has(FeatureFlagEnum $key): bool;

    public function get(FeatureFlagEnum $key): bool;

    public function hasAll(): bool;

    public function getAll(): bool;
}
