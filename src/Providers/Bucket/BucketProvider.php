<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\Bucket;

use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagUser;

class BucketProvider implements FeatureFlagsProvider
{
    private BucketContext|null $context = null;

    private BucketClient|null $client = null;

    public function __construct(
        public readonly array $config,
        public readonly LoggerInterface $logger,
    ) {
        /** @var string|null $key */
        $key = Arr::get($config, 'key');
        /** @var string $host */
        $host = Arr::get($config, 'host', BucketClient::DEFAULT_BASE_URI);
        /** @var array $options */
        $options = Arr::get($config, 'options');

        if ($key) {
            $this->client = new BucketClient($host, $key, $options, $logger);
        }
    }

    public function setUser(FeatureFlagUser $user): void
    {
        $id = (string) $user->id;

        $this->context = new BucketContext(
            id: $id,
            email: $user->email,
            context: $user->custom,
        );
    }

    public function setAnonymousUser(): void
    {
        $this->context = BucketContext::anonymous();
    }

    public function flag(FeatureFlagEnum $flag): bool
    {
        assert(is_string($flag->value));

        $client = $this->client;

        if ($client === null) {
            return false;
        }

        if ($this->context === null) {
            $this->setAnonymousUser();
        }

        assert($this->context !== null);

        return $client->getFeature($flag->value, false, context: $this->context);
    }
}
