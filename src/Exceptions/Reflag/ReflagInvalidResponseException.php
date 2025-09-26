<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Exceptions\Reflag;

use Exception;

final class ReflagInvalidResponseException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Invalid response from Reflag.'
        );
    }
}
