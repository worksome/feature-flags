<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Exceptions\LaunchDarkly;

use Exception;

final class LaunchDarklyMissingAccessTokenException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Missing access token env variable.'
        );
    }
}
