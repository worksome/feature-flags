<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Traits;

trait InteractsWithFeatureFlags
{
    public function switchFeatureFlag(string $key, bool $onOff): void
    {
        $this->app['config']->set('feature-flags.overrides.' . $key, $onOff);
    }

    public function enableFeatureFlag(string $key): void
    {

        $this->switchFeatureFlag($key, true);
    }

    public function disableFeatureFlag(string $key): void
    {

        $this->switchFeatureFlag($key, false);
    }
}
