<?php

namespace Worksome\FeatureFlags\LaunchDarkly;

use Illuminate\Support\Facades\Config;
use LaunchDarkly\LDClient;
use LaunchDarkly\LDUser;
use LaunchDarkly\LDUserBuilder;
use LaunchDarkly\Integrations\Guzzle;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;

class LaunchDarklyProvider implements FeatureFlagsProvider
{
    /** @var \LaunchDarkly\LDClient */
    protected $client;

    /** @var LDUser */
    private $user;

    public function __construct()
    {
        $options = array_merge([
            'event_publisher' => Guzzle::eventPublisher()
        ], Config::get('feature-flags.providers.launchdarkly.options') ?? []);

        if ($key = Config::get('feature-flags.providers.launchdarkly.key')) {
            $this->client = new LDClient($key, $options);
        }
    }

    public function setUser(FeatureFlagUser $user): void
    {
        $this->user = (new LDUserBuilder($user->id))
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
        if (!$this->client) {
            return false;
        }

        return filter_var($this->client->variation($flag, $this->user), FILTER_VALIDATE_BOOLEAN);
    }
}
