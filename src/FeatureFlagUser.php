<?php

namespace Worksome\FeatureFlags;

class FeatureFlagUser
{
    public function __construct(
        public string|int $id,
        public string $email,
        public array $custom = [],
    ) {
    }
}
