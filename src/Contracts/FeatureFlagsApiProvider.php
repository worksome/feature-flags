<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Contracts;

use Psr\Http\Message\ResponseInterface;

interface FeatureFlagsApiProvider
{
    public function isAccessTokenValid(): bool;

    public function get(string $featureFlagKey): ResponseInterface;

    public function clearRules(string $featureFlagKey): ResponseInterface;

    public function addRuleForEmailAddresses(
        string $featureFlagKey,
        bool $featureFlagValue,
        array $emailAddresses,
    ): ResponseInterface;

    public function enable(string $featureFlagKey): ResponseInterface;

    public function disable(string $featureFlagKey): ResponseInterface;
}
