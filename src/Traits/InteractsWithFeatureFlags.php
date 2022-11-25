<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Traits;

use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;

trait InteractsWithFeatureFlags
{
    public function switchFeatureFlag(FeatureFlagEnum $key, bool $onOff): void
    {
        $this->app['config']->set("feature-flags.overrides.{$key->value}", $onOff);
    }

    public function enableFeatureFlag(FeatureFlagEnum $key): void
    {
        $this->switchFeatureFlag($key, true);
    }

    public function disableFeatureFlag(FeatureFlagEnum $key): void
    {
        $this->switchFeatureFlag($key, false);
    }
}
