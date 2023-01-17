<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\LaunchDarkly;

use Illuminate\Support\Arr;
use LaunchDarkly\Integrations\Guzzle;
use LaunchDarkly\LDClient;
use LaunchDarkly\LDUser;
use LaunchDarkly\LDUserBuilder;
use Psr\Log\LoggerInterface;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;

class LaunchDarklyProvider implements FeatureFlagsProvider
{
    protected LDClient|null $client = null;

    private LDUser|null $user = null;

    public function __construct(array $config, LoggerInterface $logger)
    {
        /** @var array<string, mixed> $options */
        $options = Arr::get($config, 'options', []);

        /** @var array<string, mixed> $options */
        $options = array_merge([
            'event_publisher' => Guzzle::eventPublisher(),
            'logger' => $logger,
        ], $options);

        /** @var string|null $key */
        $key = Arr::get($config, 'key');

        if ($key) {
            /**  @phpstan-ignore-next-line  */
            $this->client = new LDClient($key, $options);
        }
    }

    public function setUser(FeatureFlagUser $user): void
    {
        $id = (string) $user->id;

        $this->user = (new LDUserBuilder($id))
            ->email($user->email)
            ->custom($user->custom)
            ->build();
    }

    public function setAnonymousUser(): void
    {
        $this->user = (new LDUserBuilder('anonymous'))
            ->anonymous(true)
            ->build();
    }

    public function flag(FeatureFlagEnum $flag): bool
    {
        assert(is_string($flag->value));

        $client = $this->client;

        if ($client === null) {
            return false;
        }

        if ($this->user === null) {
            $this->setAnonymousUser();
        }

        assert($this->user !== null);
        return filter_var($client->variation($flag->value, $this->user), FILTER_VALIDATE_BOOLEAN);
    }
}
