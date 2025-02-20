<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Exceptions\Bucket;

use Exception;

class BucketInvalidResponseException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Invalid response from Bucket.'
        );
    }
}
