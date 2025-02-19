<?php

declare(strict_types=1);

use OpenFeature\OpenFeatureAPI;
use OpenFeature\Providers\Flagd\FlagdProvider;
use Psr\Log\NullLogger;
use Worksome\FeatureFlags\FeatureFlagsManager;
use Worksome\FeatureFlags\FeatureFlagUser;
use Worksome\FeatureFlags\Providers\OpenFeature\OpenFeatureProvider;
use Worksome\FeatureFlags\Providers\OpenFeature\Resolvers\FlagdOpenFeatureResolver;
use Worksome\FeatureFlags\Tests\Enums\TestFeatureFlag;
use Worksome\FeatureFlags\Tests\Support\ArrayOpenFeatureResolver;

beforeEach(function () {
    ArrayOpenFeatureResolver::$lastContext = null;
    ArrayOpenFeatureResolver::$lastOptions = null;
    ArrayOpenFeatureResolver::$resolutions = 0;
    ArrayOpenFeatureResolver::$evaluations = 0;
    ArrayOpenFeatureResolver::$unavailable = false;

    $this->provider = new OpenFeatureProvider([
        'default' => 'array',
        'resolvers' => [
            'array' => [
                'resolver' => ArrayOpenFeatureResolver::class,
                'flags' => [
                    TestFeatureFlag::FlagOne->value => true,
                    TestFeatureFlag::FlagTwo->value => false,
                ],
            ],
        ],
    ], new NullLogger());
});

it('resolves flags using the configured resolver', function () {
    expect($this->provider->flag(TestFeatureFlag::FlagOne))->toBeTrue()
        ->and($this->provider->flag(TestFeatureFlag::FlagTwo))->toBeFalse()
        ->and($this->provider->flag(TestFeatureFlag::FlagThree))->toBeFalse();
});

it('passes the resolver options, excluding the resolver class, to the resolver', function () {
    $this->provider->flag(TestFeatureFlag::FlagOne);

    expect(ArrayOpenFeatureResolver::$lastOptions)->toBe([
        'flags' => [
            TestFeatureFlag::FlagOne->value => true,
            TestFeatureFlag::FlagTwo->value => false,
        ],
    ]);
});

it('only resolves the OpenFeature provider once', function () {
    $this->provider->flag(TestFeatureFlag::FlagOne);
    $this->provider->flag(TestFeatureFlag::FlagTwo);

    expect(ArrayOpenFeatureResolver::$resolutions)->toBe(1);
});

it('caches results until the user changes', function () {
    $this->provider->flag(TestFeatureFlag::FlagOne);
    $this->provider->flag(TestFeatureFlag::FlagOne);

    expect(ArrayOpenFeatureResolver::$evaluations)->toBe(1);

    $this->provider->setUser(new FeatureFlagUser(id: 123, email: 'test@example.com'));
    $this->provider->flag(TestFeatureFlag::FlagOne);

    expect(ArrayOpenFeatureResolver::$evaluations)->toBe(2);
});

it('expires cached results after the cache TTL', function () {
    $this->provider->flag(TestFeatureFlag::FlagOne);

    $this->travel(9)->seconds();
    $this->provider->flag(TestFeatureFlag::FlagOne);

    expect(ArrayOpenFeatureResolver::$evaluations)->toBe(1);

    $this->travel(2)->seconds();
    $this->provider->flag(TestFeatureFlag::FlagOne);

    expect(ArrayOpenFeatureResolver::$evaluations)->toBe(2);
});

it('stops evaluating flags while the provider is unavailable', function () {
    ArrayOpenFeatureResolver::$unavailable = true;

    expect($this->provider->flag(TestFeatureFlag::FlagOne))->toBeFalse()
        ->and($this->provider->flag(TestFeatureFlag::FlagTwo))->toBeFalse()
        ->and(ArrayOpenFeatureResolver::$evaluations)->toBe(1);

    ArrayOpenFeatureResolver::$unavailable = false;

    $this->travel(29)->seconds();

    expect($this->provider->flag(TestFeatureFlag::FlagOne))->toBeFalse()
        ->and(ArrayOpenFeatureResolver::$evaluations)->toBe(1);

    $this->travel(2)->seconds();

    expect($this->provider->flag(TestFeatureFlag::FlagOne))->toBeTrue()
        ->and(ArrayOpenFeatureResolver::$evaluations)->toBe(2);
});

it('does not stop evaluating flags when a flag does not exist', function () {
    expect($this->provider->flag(TestFeatureFlag::FlagThree))->toBeFalse()
        ->and($this->provider->flag(TestFeatureFlag::FlagOne))->toBeTrue()
        ->and(ArrayOpenFeatureResolver::$evaluations)->toBe(2);
});

it('does not override the global OpenFeature provider', function () {
    $global = OpenFeatureAPI::getInstance()->getProviderMetadata()->getName();

    $this->provider->flag(TestFeatureFlag::FlagOne);

    expect(OpenFeatureAPI::getInstance()->getProviderMetadata()->getName())->toBe($global);
});

it('times out when Flagd is unresponsive', function () {
    // The server accepts connections, but never responds.
    $server = stream_socket_server('tcp://127.0.0.1:0');
    $port = (int) parse_url('tcp://' . stream_socket_get_name($server, false), PHP_URL_PORT);

    $provider = new OpenFeatureProvider([
        'default' => 'flagd',
        'resolvers' => [
            'flagd' => ['resolver' => FlagdOpenFeatureResolver::class, 'port' => $port, 'timeout' => 0.2],
        ],
    ], new NullLogger());

    $start = microtime(true);

    expect($provider->flag(TestFeatureFlag::FlagOne))->toBeFalse()
        ->and(microtime(true) - $start)->toBeLessThan(2);

    fclose($server);
});

it('evaluates anonymously when no user has been set', function () {
    $this->provider->flag(TestFeatureFlag::FlagOne);

    expect(ArrayOpenFeatureResolver::$lastContext?->getTargetingKey())->toBe('anonymous');
});

it('passes the user to the evaluation context', function () {
    $this->provider->setUser(new FeatureFlagUser(id: 123, email: 'test@example.com', custom: ['plan' => 'pro']));

    $this->provider->flag(TestFeatureFlag::FlagOne);

    $context = ArrayOpenFeatureResolver::$lastContext;

    expect($context?->getTargetingKey())->toBe('123')
        ->and($context?->getAttributes()->get('email'))->toBe('test@example.com')
        ->and($context?->getAttributes()->get('plan'))->toBe('pro');
});

it('converts custom attributes to types supported by OpenFeature', function () {
    $this->provider->setUser(new FeatureFlagUser(id: 123, email: 'test@example.com', custom: [
        'created_at' => new DateTimeImmutable('2026-01-01 00:00:00'),
        'flag' => TestFeatureFlag::FlagOne,
        'nested' => ['flags' => [TestFeatureFlag::FlagTwo], 'object' => new stdClass()],
        'object' => new stdClass(),
        'plan' => 'pro',
    ]));

    $this->provider->flag(TestFeatureFlag::FlagOne);

    $attributes = ArrayOpenFeatureResolver::$lastContext?->getAttributes();

    expect($attributes?->get('created_at'))->toEqual(new DateTime('2026-01-01 00:00:00'))
        ->and($attributes?->get('flag'))->toBe('flag-1')
        ->and($attributes?->get('nested'))->toBe(['flags' => ['flag-2']])
        ->and($attributes?->keys())->not->toContain('object')
        ->and($attributes?->get('plan'))->toBe('pro');
});

it('throws when no default resolver is configured', function () {
    $provider = new OpenFeatureProvider(['default' => null, 'resolvers' => []], new NullLogger());

    $provider->flag(TestFeatureFlag::FlagOne);
})->throws(RuntimeException::class, 'The `open_feature` driver requires a default resolver.');

it('throws when the resolver is not configured', function () {
    $provider = new OpenFeatureProvider(['default' => 'missing', 'resolvers' => []], new NullLogger());

    $provider->flag(TestFeatureFlag::FlagOne);
})->throws(RuntimeException::class, 'The `open_feature` resolver `missing` is not configured.');

it('throws when the resolver does not implement the contract', function () {
    $provider = new OpenFeatureProvider([
        'default' => 'invalid',
        'resolvers' => ['invalid' => ['resolver' => stdClass::class]],
    ], new NullLogger());

    $provider->flag(TestFeatureFlag::FlagOne);
})->throws(RuntimeException::class, 'must implement');

it('can resolve the flagd resolver from the container', function () {
    $resolver = app(FlagdOpenFeatureResolver::class);

    expect($resolver([
        'host' => null,
        'port' => '8014',
        'secure' => 'false',
    ]))->toBeInstanceOf(FlagdProvider::class);
});

it('can be created from the default config', function () {
    // Nothing is listening on this port, so the flag should be disabled rather than throwing.
    config()->set('feature-flags.providers.open_feature.resolvers.flagd.port', 1);

    $provider = app(FeatureFlagsManager::class)->driver('open_feature');

    expect($provider)->toBeInstanceOf(OpenFeatureProvider::class)
        ->and($provider->flag(TestFeatureFlag::FlagOne))->toBeFalse();
});
