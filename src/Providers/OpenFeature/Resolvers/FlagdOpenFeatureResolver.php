<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\OpenFeature\Resolvers;

use Illuminate\Support\Arr;
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
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    /** @param array{protocol?: string, host?: string, port?: int|numeric-string, secure?: bool} $options */
    public function __invoke(array $options): Provider
    {
        if (! class_exists(FlagdProvider::class)) {
            throw new RuntimeException('The `open-feature/flagd-provider` package is required.');
        }

        return new FlagdProvider([
            'protocol' => Arr::get($options, 'protocol', 'tcp'),
            'host' => Arr::get($options, 'host', 'localhost'),
            'port' => Arr::get($options, 'port', 8013),
            'secure' => Arr::get($options, 'secure', true),
            'http' => [
                'client' => $this->client,
                'requestFactory' => $this->requestFactory,
                'streamFactory' => $this->streamFactory,
            ],
        ]);
    }
}
