<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Contracts;

use Worksome\FeatureFlags\FeatureFlagUser;

interface FeatureFlagsProvider
{
    public function flag(FeatureFlagEnum $flag): bool;

    public function setUser(FeatureFlagUser $user): void;

    public function setAnonymousUser(): void;
}
