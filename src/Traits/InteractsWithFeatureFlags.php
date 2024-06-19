<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Traits;

use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagOverrider;

/**
 * This class is intended for usage mostly in testing context
 * It provides the necessary methods to interact with the current feature flag overrider.
 * Therefore, easily turning flag ON and OFF
 */
trait InteractsWithFeatureFlags
{
    public function switchFeatureFlag(FeatureFlagEnum $key, bool|null $onOffNull): void
    {
        $this->featureFlagOverrider()->set($key, $onOffNull);
    }

    public function enableFeatureFlag(FeatureFlagEnum $key): void
    {
        $this->switchFeatureFlag($key, true);
    }

    public function disableFeatureFlag(FeatureFlagEnum $key): void
    {
        $this->switchFeatureFlag($key, false);
    }

    public function switchFeatureFlagAll(bool|null $onOffNull): void
    {
        $this->featureFlagOverrider()->setAll($onOffNull);
    }

    public function enableFeatureFlagAll(): void
    {
        $this->switchFeatureFlagAll(true);
    }

    public function disableFeatureFlagAll(): void
    {
        $this->switchFeatureFlagAll(false);
    }

    public function clearFeatureFlag(FeatureFlagEnum $key): void
    {
        $this->switchFeatureFlag($key, null);
    }

    public function clearFeatureFlagAll(): void
    {
        $this->switchFeatureFlagAll(null);
    }

    protected function featureFlagOverrider(): FeatureFlagOverrider
    {
        return $this->app->get(FeatureFlagOverrider::class);
    }
}
