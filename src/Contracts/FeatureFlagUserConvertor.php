<?php

namespace Worksome\FeatureFlags\Contracts;

use Worksome\FeatureFlags\FeatureFlagUser;

interface FeatureFlagUserConvertor {
    public function convert(object $user): FeatureFlagUser;
}
