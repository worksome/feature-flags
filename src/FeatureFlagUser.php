<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

class FeatureFlagUser
{
    /** @param array<string, mixed> $custom */
    public function __construct(
        public string|int $id,
        public string $email,
        public array $custom = [],
    ) {
    }
}
