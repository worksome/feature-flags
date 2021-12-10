<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

class FeatureFlagUser
{
    public function __construct(
        public string $id,
        public string $email,
        public array $custom = [],
    ) {
    }
}
