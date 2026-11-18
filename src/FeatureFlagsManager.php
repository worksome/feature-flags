<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Support\Manager;
use Psr\Log\LoggerInterface;
use Worksome\FeatureFlags\Providers\FakeProvider;
use Worksome\FeatureFlags\Providers\LaunchDarkly\LaunchDarklyProvider;
use Worksome\FeatureFlags\Providers\OpenFeature\OpenFeatureProvider;
use Worksome\FeatureFlags\Providers\Reflag\ReflagProvider;

/** @phpstan-import-type OpenFeatureConfig from OpenFeatureProvider */
class FeatureFlagsManager extends Manager
{
    public function createReflagDriver(): ReflagProvider
    {
        /** @var array $config */
        $config = $this->config->get('feature-flags.providers.reflag');
        /** @var LoggerInterface $logger */
        $logger = $this->getContainer()->get(LoggerInterface::class);

        return new ReflagProvider(
            $config,
            $logger,
        );
    }

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

    public function createOpenFeatureDriver(): OpenFeatureProvider
    {
        /** @var OpenFeatureConfig $config */
        $config = $this->config->get('feature-flags.providers.open_feature');
        /** @var LoggerInterface $logger */
        $logger = $this->getContainer()->get(LoggerInterface::class);

        return new OpenFeatureProvider(
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
