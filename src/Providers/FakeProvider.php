<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers;

use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;

class FakeProvider implements FeatureFlagsProvider
{
    /** @var string|FeatureFlagUser */
    public string|FeatureFlagUser $user;

    /** @var array<string, bool> */
    private array $flags = [];

    public function setUser(FeatureFlagUser $user): void
    {
        $this->user = $user;
    }

    public function setAnonymousUser(): void
    {
        $this->user = 'anonymous';
    }

    public function flag(FeatureFlagEnum $flag): bool
    {
        if (! isset($this->flags[$flag->value])) {
            return false;
        }

        return $this->flags[$flag->value];
    }

    public function setFlag(FeatureFlagEnum $key, bool $value): void
    {
        $this->flags[$key->value] = $value;
    }
}
