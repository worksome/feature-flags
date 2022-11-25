<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Tests\Enums;

use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;

enum TestFeatureFlag: string implements FeatureFlagEnum
{
    case TestFlag = 'test';
    case AmazingFeature = 'amazing-feature';
    case FlagOne = 'flag-1';
    case FlagTwo = 'flag-2';
    case FlagThree = 'flag-3';
}
