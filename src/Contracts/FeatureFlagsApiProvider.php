<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Contracts;

use Psr\Http\Message\ResponseInterface;

interface FeatureFlagsApiProvider
{
    public function isAccessTokenValid(): bool;

    public function get(FeatureFlagEnum $featureFlagKey): ResponseInterface;

    public function clearRules(FeatureFlagEnum $featureFlagKey): ResponseInterface;

    public function addRuleForEmailAddresses(
        FeatureFlagEnum $featureFlagKey,
        bool $featureFlagValue,
        array $emailAddresses,
    ): ResponseInterface;

    public function enable(FeatureFlagEnum $featureFlagKey): ResponseInterface;

    public function disable(FeatureFlagEnum $featureFlagKey): ResponseInterface;
}
