<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;

class FeatureFlagsOverrideProvider implements FeatureFlagsProvider
{
    public function __construct(
        private FeatureFlagsProvider $provider,
        private FeatureFlagsOverridesRepository $overrides,
    ) {
    }

    public function flag(string $flag): bool
    {
        if ($this->overrides->has($flag)) {
            return $this->overrides->get($flag);
        }

        return $this->provider->flag($flag);
    }

    public function setUser(FeatureFlagUser $user): void
    {
        $this->provider->setUser($user);
    }

    public function setAnonymousUser(): void
    {
        $this->provider->setAnonymousUser();
    }
}
