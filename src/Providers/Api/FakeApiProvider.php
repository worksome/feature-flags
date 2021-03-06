<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\Api;

use Worksome\FeatureFlags\Contracts\FeatureFlagsApiProvider;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class FakeApiProvider implements FeatureFlagsApiProvider
{
    public function __construct(
        private bool $hasAccessToken = true,
    ) {
        if (! $hasAccessToken) {
            throw new LaunchDarklyMissingAccessTokenException();
        }
    }

    public function isAccessTokenValid(): bool
    {
        if (!$this->hasAccessToken) {
            return false;
        }

        return true;
    }

    public function get(string $featureFlagKey): ResponseInterface
    {
        return $this->response();
    }

    public function clearRules(string $featureFlagKey): ResponseInterface
    {
        return $this->response();
    }

    public function addRuleForEmailAddresses(
        string $featureFlagKey,
        bool $featureFlagValue,
        array $emailAddresses,
    ): ResponseInterface {
        return $this->response();
    }

    public function enable(string $featureFlagKey): ResponseInterface
    {
        return $this->response();
    }

    public function disable(string $featureFlagKey): ResponseInterface
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
