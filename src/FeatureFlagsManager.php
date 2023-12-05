<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use Psr\Log\LoggerInterface;
use Worksome\FeatureFlags\Providers\FakeProvider;
use Worksome\FeatureFlags\Providers\LaunchDarkly\LaunchDarklyProvider;

class FeatureFlagsManager extends Manager
{
    public function createLaunchDarklyDriver(): LaunchDarklyProvider
    {
        /** @var array $config */
        $config = $this->config->get('feature-flags.providers.launchdarkly');
        /** @var LoggerInterface $logger */
        $logger = $this->getContainer()->get(LoggerInterface::class);

        return new LaunchDarklyProvider(
            $config,
            $logger,
        );
    }

    public function createFakeDriver(): FakeProvider
    {
        return new FakeProvider();
    }

    public function getDefaultDriver(): string
    {
        return strval($this->config->get('feature-flags.default')); // @phpstan-ignore-line
    }
}
