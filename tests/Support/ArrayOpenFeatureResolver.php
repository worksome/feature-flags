<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Tests\Support;

use OpenFeature\implementation\provider\AbstractProvider;
use OpenFeature\implementation\provider\ResolutionDetailsBuilder;
use OpenFeature\implementation\provider\ResolutionDetailsFactory;
use OpenFeature\implementation\provider\ResolutionError;
use OpenFeature\interfaces\flags\EvaluationContext;
use OpenFeature\interfaces\provider\ErrorCode;
use OpenFeature\interfaces\provider\Provider;
use OpenFeature\interfaces\provider\ResolutionDetails;
use RuntimeException;
use Worksome\FeatureFlags\Providers\OpenFeature\Contracts\OpenFeatureResolver;

class ArrayOpenFeatureResolver implements OpenFeatureResolver
{
    public static EvaluationContext|null $lastContext = null;

    /** @var array<array-key, mixed>|null */
    public static array|null $lastOptions = null;

    public static int $resolutions = 0;

    public static int $evaluations = 0;

    public static bool $unavailable = false;

    public function __invoke(array $options): Provider
    {
        self::$lastOptions = $options;
        self::$resolutions++;

        /** @var array<string, bool> $flags */
        $flags = $options['flags'] ?? [];

        return new class($flags) extends AbstractProvider {
            /** @param array<string, bool> $flags */
            public function __construct(private readonly array $flags)
            {
            }

            public function resolveBooleanValue(
                string $flagKey,
                bool $defaultValue,
                EvaluationContext|null $context = null,
            ): ResolutionDetails {
                ArrayOpenFeatureResolver::$lastContext = $context;
                ArrayOpenFeatureResolver::$evaluations++;

                if (ArrayOpenFeatureResolver::$unavailable) {
                    throw new RuntimeException('Connection refused');
                }

                if (! array_key_exists($flagKey, $this->flags)) {
                    return (new ResolutionDetailsBuilder())
                        ->withValue($defaultValue)
                        ->withError(new ResolutionError(ErrorCode::FLAG_NOT_FOUND()))
                        ->build();
                }

                return ResolutionDetailsFactory::fromSuccess($this->flags[$flagKey]);
            }

            public function resolveStringValue(
                string $flagKey,
                string $defaultValue,
                EvaluationContext|null $context = null,
            ): ResolutionDetails {
                return ResolutionDetailsFactory::fromSuccess($defaultValue);
            }

            public function resolveIntegerValue(
                string $flagKey,
                int $defaultValue,
                EvaluationContext|null $context = null,
            ): ResolutionDetails {
                return ResolutionDetailsFactory::fromSuccess($defaultValue);
            }

            public function resolveFloatValue(
                string $flagKey,
                float $defaultValue,
                EvaluationContext|null $context = null,
            ): ResolutionDetails {
                return ResolutionDetailsFactory::fromSuccess($defaultValue);
            }

            /** @param array<mixed> $defaultValue */
            public function resolveObjectValue(
                string $flagKey,
                array $defaultValue,
                EvaluationContext|null $context = null,
            ): ResolutionDetails {
                return ResolutionDetailsFactory::fromSuccess($defaultValue);
            }
        };
    }
}
