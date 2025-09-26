<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\Reflag;

use Illuminate\Support\Arr;

readonly class ReflagContext
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
        $userContext = isset($this->context['user']) && is_array($this->context['user'])
            ? $this->context['user']
            : [];

        $companyContext = isset($this->context['company']) && is_array($this->context['company'])
            ? $this->context['company']
            : [];

        return Arr::dot([
            'context' => [
                'user' => [
                    ... $userContext,
                    'id' => $this->id,
                    'email' => $this->email,
                ],
                'company' => [
                    ... $companyContext,
                ],
                'other' => [
                    ... Arr::except($this->context, ['user', 'company']),
                ],
            ],
        ]);
    }
}
