<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\OpenFeature;

use BackedEnum;
use DateTime;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use OpenFeature\implementation\flags\Attributes;
use OpenFeature\implementation\flags\EvaluationContext;
use OpenFeature\interfaces\flags\Client;
use OpenFeature\interfaces\flags\EvaluationContext as EvaluationContextContract;
use OpenFeature\interfaces\provider\ErrorCode;
use OpenFeature\isolated\OpenFeatureAPIFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Stringable;
use UnitEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;
use Worksome\FeatureFlags\Providers\OpenFeature\Contracts\OpenFeatureResolver;

/**
 * @phpstan-type OpenFeatureConfig array{
 *     default: string|null,
 *     resolvers: array<string, array{resolver: class-string<OpenFeatureResolver>}&array<string, mixed>>,
 *     cache_ttl?: int,
 *     retry_after?: int,
 * }
 */
class OpenFeatureProvider implements FeatureFlagsProvider
{
    /** Errors that indicate the provider itself is unavailable, rather than a problem with a single flag. */
    private const array UNAVAILABLE_ERROR_CODES = ['GENERAL', 'PARSE_ERROR', 'PROVIDER_NOT_READY'];

    private EvaluationContextContract|null $context = null;

    private Client|null $client = null;

    /** @var array<string, bool> */
    private array $results = [];

    private int $resultsExpireAt = 0;

    private int $unavailableUntil = 0;

    /** @param OpenFeatureConfig $config */
    public function __construct(public readonly array $config, public readonly LoggerInterface $logger)
    {
    }

    public function setUser(FeatureFlagUser $user): void
    {
        $id = (string) $user->id;

        $this->context = new EvaluationContext(
            targetingKey: $id,
            attributes: new Attributes(['email' => $user->email, ...$this->normaliseAttributes($user->custom)]),
        );

        $this->results = [];
    }

    public function setAnonymousUser(): void
    {
        $this->context = new EvaluationContext(
            targetingKey: 'anonymous',
        );

        $this->results = [];
    }

    public function flag(FeatureFlagEnum $flag): bool
    {
        assert(is_string($flag->value)); // @phpstan-ignore function.alreadyNarrowedType, function.alreadyNarrowedType

        if ($this->context === null) {
            $this->setAnonymousUser();
        }

        assert($this->context !== null);

        $now = Carbon::now()->getTimestamp();

        if ($now < $this->unavailableUntil) {
            return false;
        }

        // Results are cached briefly, as long-running processes (e.g. queue workers) may never change user.
        if ($now >= $this->resultsExpireAt) {
            $this->results = [];
            $this->resultsExpireAt = $now + ($this->config['cache_ttl'] ?? 10);
        }

        if (array_key_exists($flag->value, $this->results)) {
            return $this->results[$flag->value];
        }

        $details = $this->client()->getBooleanDetails($flag->value, false, $this->context);

        $errorCode = $details->getError()?->getResolutionErrorCode();

        if ($errorCode instanceof ErrorCode && in_array($errorCode->getValue(), self::UNAVAILABLE_ERROR_CODES, true)) {
            $retryAfter = $this->config['retry_after'] ?? 30;

            $this->unavailableUntil = $now + $retryAfter;

            $this->logger->warning(
                'The OpenFeature provider is unavailable, all flags will be disabled for {retryAfter} seconds.',
                ['retryAfter' => $retryAfter, 'errorCode' => $errorCode->getValue()],
            );

            return false;
        }

        return $this->results[$flag->value] = (bool) $details->getValue();
    }

    /**
     * OpenFeature attributes only support scalars, dates and arrays, so other values are converted or removed.
     *
     * @param array<array-key, mixed> $attributes
     *
     * @return array<array-key, bool|string|int|float|DateTime|array<mixed>|null>
     */
    private function normaliseAttributes(array $attributes): array
    {
        $normalised = [];

        foreach ($attributes as $key => $value) {
            $value = match (true) {
                is_array($value) => $this->normaliseAttributes($value),
                $value instanceof DateTime => $value,
                $value instanceof DateTimeInterface => DateTime::createFromInterface($value),
                $value instanceof BackedEnum => $value->value,
                $value instanceof UnitEnum => $value->name,
                $value instanceof Stringable => (string) $value,
                default => $value,
            };

            if ($value === null || is_scalar($value) || is_array($value) || $value instanceof DateTime) {
                $normalised[$key] = $value;
            }
        }

        return $normalised;
    }

    private function client(): Client
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $name = $this->config['default'] ?? null;

        if ($name === null) {
            throw new RuntimeException('The `open_feature` driver requires a default resolver.');
        }

        $options = $this->config['resolvers'][$name] ?? null;

        if ($options === null) {
            throw new RuntimeException(sprintf('The `open_feature` resolver `%s` is not configured.', $name));
        }

        // @phpstan-ignore larastanStrictRules.noGlobalLaravelFunction
        $resolver = app($options['resolver']);

        if (! $resolver instanceof OpenFeatureResolver) {
            throw new RuntimeException(
                sprintf('The `open_feature` resolver `%s` must implement `%s`.', $name, OpenFeatureResolver::class)
            );
        }

        unset($options['resolver']);

        // An isolated API is used, so that the global `OpenFeatureAPI` instance is not overridden.
        $openFeature = OpenFeatureAPIFactory::createAPI();

        $openFeature->setProvider($resolver($options));

        $openFeature->setLogger($this->logger);

        return $this->client = $openFeature->getClient(null, null);
    }
}
