<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\Reflag;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use SensitiveParameter;
use Worksome\FeatureFlags\Exceptions\Reflag\ReflagInvalidResponseException;

class ReflagClient
{
    public const string DEFAULT_BASE_URI = 'https://front-eu.reflag.com';

    private Client $client;

    public function __construct(
        private readonly string $baseUri,
        #[SensitiveParameter]
        private readonly string $sdkKey,
        private readonly array $options,
        private readonly LoggerInterface $logger,
    ) {
        $stack = HandlerStack::create();

        $defaults = [
            'headers' => self::defaultHeaders($this->sdkKey, $this->options),
            'timeout' => Arr::get($this->options, 'timeout'),
            'connect_timeout' => Arr::get($this->options, 'connect_timeout'),
            'handler' => $stack,
            'debug' => Arr::get($this->options, 'debug', false),
            'base_uri' => $this->baseUri,
        ];

        $this->client = new Client($defaults);
    }

    public function getFeature(string $value, bool $defaultValue, ReflagContext|null $context = null): bool
    {
        $features = $this->getAllFeatures($context);

        if (! isset($features[$value])) {
            $this->logger->warning("ReflagClient::getFeature: Feature flag does not exist for key: {$value}");

            return $defaultValue;
        }

        return $features[$value];
    }

    /** @return array<string, bool> */
    public function getAllFeatures(ReflagContext|null $context = null): array
    {
        $transformedContext = $context?->transform() ?? [];

        try {
            $response = $this->client->get('/features/enabled', [
                'query' => $transformedContext,
            ]);

            $body = $response->getBody();

            /** @var array{success?: bool, features?: array<string, array{isEnabled: bool}>} $response */
            $response = json_decode($body->getContents(), true);

            throw_unless($response['success'] ?? false, ReflagInvalidResponseException::class);

            /** @phpstan-ignore return.type */
            return Arr::mapWithKeys(
                $response['features'] ?? [],
                fn (array $value, string $key): array => [$key => $value['isEnabled']],
            );
        } catch (BadResponseException $e) {
            $this->logger->warning(
                "ReflagClient::getAllFeatures: (code {$e->getResponse()->getStatusCode()}) {$e->getMessage()}"
            );

            return [];
        }
    }

    private static function defaultHeaders(string $sdkKey, array $options): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$sdkKey}",
        ];
    }
}
