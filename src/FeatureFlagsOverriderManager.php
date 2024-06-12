<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use Worksome\FeatureFlags\Overriders\ConfigOverrider;
use Worksome\FeatureFlags\Overriders\InMemoryOverrider;

class FeatureFlagsOverriderManager extends Manager
{
    public function createConfigDriver(): ConfigOverrider
    {
        return new ConfigOverrider(
            $this->config,
        );
    }

    public function createInMemoryDriver(): InMemoryOverrider
    {
        return new InMemoryOverrider();
    }

    public function getDefaultDriver(): string
    {
        return strval($this->config->get('feature-flags.overrider')); // @phpstan-ignore-line
    }
}
