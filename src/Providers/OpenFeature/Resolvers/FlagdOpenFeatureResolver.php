<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\OpenFeature\Resolvers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use OpenFeature\interfaces\provider\Provider;
use OpenFeature\Providers\Flagd\FlagdProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use Worksome\FeatureFlags\Providers\OpenFeature\Contracts\OpenFeatureResolver;

/** @link https://packagist.org/packages/open-feature/flagd-provider */
readonly class FlagdOpenFeatureResolver implements OpenFeatureResolver
{
    public function __construct(
        private ClientInterface|null $client = null,
        private RequestFactoryInterface $requestFactory = new HttpFactory(),
        private StreamFactoryInterface $streamFactory = new HttpFactory(),
    ) {
    }

    /**
     * @param array{
     *     host?: string|null,
     *     port?: int|numeric-string|null,
     *     secure?: bool|string|null,
     *     timeout?: float|numeric-string|null,
     * } $options
     */
    public function __invoke(array $options): Provider
    {
        if (! class_exists(FlagdProvider::class)) {
            throw new RuntimeException('The `open-feature/flagd-provider` package is required.');
        }

        // Flags are evaluated synchronously, so a timeout prevents requests from hanging if Flagd is unresponsive.
        $timeout = (float) ($options['timeout'] ?? 1.0);

        // Values are typically sourced from `env()`, so normalise them to the types expected by the Flagd validator.
        return new FlagdProvider([
            // The Flagd provider only implements HTTP, any other protocol falls back to it.
            'protocol' => 'http',
            'host' => $options['host'] ?? 'localhost',
            'port' => (int) ($options['port'] ?? 8013),
            'secure' => filter_var($options['secure'] ?? false, FILTER_VALIDATE_BOOL),
            'httpConfig' => [
                'client' => $this->client ?? new Client([
                    'timeout' => $timeout,
                    'connect_timeout' => $timeout,
                ]),
                'requestFactory' => $this->requestFactory,
                'streamFactory' => $this->streamFactory,
            ],
        ]);
    }
}
