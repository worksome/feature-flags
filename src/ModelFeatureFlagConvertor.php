<?php

namespace Worksome\FeatureFlags;

use InvalidArgumentException;
use Worksome\FeatureFlags\Contracts\FeatureFlagUserConvertor;
use Worksome\FeatureFlags\FeatureFlagUser;

class ModelFeatureFlagConvertor implements FeatureFlagUserConvertor
{
    public function convert(object $user): FeatureFlagUser
    {
        if (!property_exists($user, 'id')) {
            throw new InvalidArgumentException('User must have an id');
        }

        if (!property_exists($user, 'email')) {
            throw new InvalidArgumentException('User must have an email');
        }

        return new FeatureFlagUser($user->id, $user->email);
    }
}
