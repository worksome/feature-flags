<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Contracts;

/**
 * @property string $name
 * @property string $value
 */
interface FeatureFlagEnum extends \BackedEnum
{
}
