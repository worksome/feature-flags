<?php
namespace Worksome\FeatureFlags;

class FeatureFlagUser
{
    public function __construct(
        public string $id,
        public string $email,
        public array $custom = []
    ) {
    }
}
