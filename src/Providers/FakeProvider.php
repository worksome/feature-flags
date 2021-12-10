<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers;

use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;

class FakeProvider implements FeatureFlagsProvider
{
    /** @var string|FeatureFlagUser */
    public $user;

    /** @var array<string,bool> */
    private $flags = [];

    public function setUser(FeatureFlagUser $user): void
    {
        $this->user = $user;
    }

    public function setAnonymousUser(): void
    {
        $this->user = 'anonymous';
    }

    public function flag(string $flag): bool
    {
        if (!isset($this->flags[$flag])) {
            return false;
        }

        return $this->flags[$flag];
    }

    public function setFlag(string $key, bool $value): void
    {
        $this->flags[$key] = $value;
    }
}
