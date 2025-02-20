<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\Bucket;

use Illuminate\Support\Arr;

readonly class BucketContext
{
    /** @param array<string, mixed> $context */
    public function __construct(public string $id, public string|null $email = null, public array $context = [])
    {
    }

    public static function anonymous(): self
    {
        return new self('anonymous');
    }

    public function transform(): array
    {
        return Arr::dot([
            'context' => [
                'user' => [
                    'id' => $this->id,
                    'email' => $this->email,
                ],
                ... $this->context,
            ],
        ]);
    }
}
