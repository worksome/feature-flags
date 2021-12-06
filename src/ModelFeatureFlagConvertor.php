<?php

namespace Worksome\FeatureFlags;

use Worksome\FeatureFlags\Contracts\FeatureFlagUserConvertor;
use Worksome\FeatureFlags\FeatureFlagUser;

class ModelFeatureFlagConvertor implements FeatureFlagUserConvertor
{
    public function convert(object $user): FeatureFlagUser
    {
        return new FeatureFlagUser($user->id, $user->email);
    }
}
