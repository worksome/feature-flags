<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Contracts;

interface FeatureFlagOverrider
{
    public function has(string $key): bool;

    public function get(string $key): bool;

    public function hasAll(): bool;

    public function getAll(): bool;
}
