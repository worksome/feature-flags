<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\LaunchDarkly;

use Illuminate\Support\Facades\Config;
use LaunchDarkly\LDClient;
use LaunchDarkly\LDUser;
use LaunchDarkly\LDUserBuilder;
use LaunchDarkly\Integrations\Guzzle;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;

class LaunchDarklyProvider implements FeatureFlagsProvider
{
    /** @var LDClient|null */
    protected $client;

    /** @var LDUser */
    private $user;

    public function __construct()
    {
        /** @var array<string,mixed> */
        $config = Config::get('feature-flags.providers.launchdarkly.options', []);

        /** @var array<string,mixed> */
        $options = array_merge([
            'event_publisher' => Guzzle::eventPublisher()
        ], $config);

        /** @var string $key */
        $key = Config::get('feature-flags.providers.launchdarkly.key');

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

    public function flag(string $flag): bool
    {
        $client = $this->client;

        if ($client === null) {
            return false;
        }

        if ($this->user === null) {
            $this->setAnonymousUser();
        }

        return filter_var($client->variation($flag, $this->user), FILTER_VALIDATE_BOOLEAN);
    }
}
