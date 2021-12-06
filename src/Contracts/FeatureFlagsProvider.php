<?php

namespace Worksome\FeatureFlags\Contracts;

use Worksome\FeatureFlags\FeatureFlagUser;

interface FeatureFlagsProvider
{
    public function flag(string $flag): bool;

    public function setUser(FeatureFlagUser $user): void;

    public function setAnonymousUser(): void;
}
