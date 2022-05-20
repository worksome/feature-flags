<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\Api;

use Worksome\FeatureFlags\Contracts\FeatureFlagsApiProvider;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;
use Psr\Http\Message\ResponseInterface;

class FakeApiProvider implements FeatureFlagsApiProvider
{
    public function __construct(
        private ResponseInterface $response,
        private bool $hasAccessToken = true,
    ) {
        if (! $hasAccessToken) {
            throw new LaunchDarklyMissingAccessTokenException();
        }
    }

    public function isAccessTokenValid(): bool
    {
        if (!$this->hasAccessToken) {
            return false;
        }

        return true;
    }

    public function get(string $featureFlagKey): ResponseInterface
    {
        return $this->response;
    }

    public function clearRules(string $featureFlagKey): ResponseInterface
    {
        return $this->response;
    }

    public function addRuleForEmailAddresses(
        string $featureFlagKey,
        bool $featureFlagValue,
        array $emailAddresses,
    ): ResponseInterface {
        return $this->response;
    }

    public function enable(string $featureFlagKey): ResponseInterface
    {
        return $this->response;
    }

    public function disable(string $featureFlagKey): ResponseInterface
    {
        return $this->response;
    }
}
