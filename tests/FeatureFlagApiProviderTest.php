<?php

declare(strict_types=1);

use Worksome\FeatureFlags\Providers\Api\FakeApiProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;

beforeEach(function () {
    $this->mock = new MockHandler([
        new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
    ]);
    $this->handlerStack = HandlerStack::create($this->mock);
    $this->client = new Client(['handler' => $this->handlerStack]);
    $this->response = $this->client->request('GET', '/');
});

it('should throw exception if access token is missing', function () {
    $this->fakeProvider = new FakeApiProvider($this->response, false);
})->throws(LaunchDarklyMissingAccessTokenException::class);

it('should return true for isAccessTokenValid', function () {
    $this->fakeProvider = new FakeApiProvider($this->response);
    expect($this->fakeProvider->isAccessTokenValid())->toBetruthy();
});
