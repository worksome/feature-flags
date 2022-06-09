<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Exceptions\LaunchDarkly;

use Exception;

final class LaunchDarklyUnavailableException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Cannot reach LaunchDarkly API.'
        );
    }
}
