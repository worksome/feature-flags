<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\OpenFeature\Contracts;

use OpenFeature\interfaces\provider\Provider;

interface OpenFeatureResolver
{
    public function __invoke(array $options): Provider;
}
