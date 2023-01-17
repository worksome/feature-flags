<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagsApiProvider;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;

class FakeApiProvider implements FeatureFlagsApiProvider
{
    public function __construct(
        private readonly bool $hasAccessToken = true,
    ) {
        if (! $hasAccessToken) {
            throw new LaunchDarklyMissingAccessTokenException();
        }
    }

    public function isAccessTokenValid(): bool
    {
        if (! $this->hasAccessToken) {
            return false;
        }

        return true;
    }

    public function get(FeatureFlagEnum $featureFlagKey): ResponseInterface
    {
        return $this->response();
    }

    public function clearRules(FeatureFlagEnum $featureFlagKey): ResponseInterface
    {
        return $this->response();
    }

    public function addRuleForEmailAddresses(
        FeatureFlagEnum $featureFlagKey,
        bool $featureFlagValue,
        array $emailAddresses,
    ): ResponseInterface {
        return $this->response();
    }

    public function enable(FeatureFlagEnum $featureFlagKey): ResponseInterface
    {
        return $this->response();
    }

    public function disable(FeatureFlagEnum $featureFlagKey): ResponseInterface
    {
        return $this->response();
    }

    private function response(): ResponseInterface
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $response = $client->request('GET', '/');

        return $response;
    }
}
