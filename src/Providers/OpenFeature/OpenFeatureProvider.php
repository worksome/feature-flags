<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\OpenFeature;

use OpenFeature\implementation\flags\Attributes;
use OpenFeature\implementation\flags\EvaluationContext;
use OpenFeature\interfaces\flags\EvaluationContext as EvaluationContextContract;
use OpenFeature\OpenFeatureAPI;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;
use Worksome\FeatureFlags\Providers\OpenFeature\Contracts\OpenFeatureResolver;

class OpenFeatureProvider implements FeatureFlagsProvider
{
    private EvaluationContextContract|null $context = null;

    /** @param array{resolver: class-string<OpenFeatureResolver>|null, options: array<string, mixed>} $config */
    public function __construct(public readonly array $config, public readonly LoggerInterface $logger)
    {
    }

    public function setUser(FeatureFlagUser $user): void
    {
        $id = (string) $user->id;

        $this->context = new EvaluationContext(
            targetingKey: $id,
            attributes: new Attributes(['email' => $user->email, ... $user->custom]), // @phpstan-ignore argument.type
        );
    }

    public function setAnonymousUser(): void
    {
        $this->context = new EvaluationContext(
            targetingKey: 'anonymous',
        );
    }

    public function flag(FeatureFlagEnum $flag): bool
    {
        assert(is_string($flag->value)); // @phpstan-ignore function.alreadyNarrowedType, function.alreadyNarrowedType

        $resolver = $this->config['resolver'] ?? null;

        if ($resolver === null) {
            throw new RuntimeException('The `open_feature` driver requires a resolver.');
        }

        // @phpstan-ignore larastanStrictRules.noGlobalLaravelFunction
        $provider = app($resolver);

        if (! $provider instanceof OpenFeatureResolver) {
            return false;
        }

        if ($this->context === null) {
            $this->setAnonymousUser();
        }

        assert($this->context !== null);

        $openFeature = OpenFeatureAPI::getInstance();

        $openFeature->setProvider($provider($this->config['options']));

        $openFeature->setLogger($this->logger);

        // @phpstan-ignore arguments.count
        return (bool) $openFeature->getClient()->getBooleanValue($flag->value, false, context: $this->context);
    }
}
