<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Exceptions\Bucket;

use Exception;

final class BucketMissingAccessTokenException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Missing access token env variable.'
        );
    }
}
