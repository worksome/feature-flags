# Feature Flags

[![PHPStan](https://img.shields.io/github/actions/workflow/status/worksome/feature-flags/static.yml?branch=main&style=flat-square&label=Static%20Analysis)](https://github.com/worksome/feature-flags/actions/workflows/static.yml)
[![Run Tests](https://img.shields.io/github/actions/workflow/status/worksome/feature-flags/tests.yml?branch=main&style=flat-square&label=Tests)](https://github.com/worksome/feature-flags/actions/workflows/tests.yml)

A package to manage feature flags in your application. Currently supporting these providers:

- [LaunchDarkly](https://launchdarkly.com)
- [Reflag](https://reflag.com)
- [OpenFeature](https://openfeature.dev) (see [below](#openfeature))

## Installation

You can install the package via composer:

```bash
composer require worksome/feature-flags
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="feature-flags-config"
```

See the [config file](config/feature-flags.php) for more information.

### Creating Feature Flags

Feature flags should be registered using one or more enums containing your feature flags.

All feature flag enums must implement the `Worksome\FeatureFlags\Contracts\FeatureFlagEnum` contract.

For example, if you had a feature flag called `flag-one`, you could create an enum with the following:

```php
namespace App\Enums;

enum FeatureFlag: string implements \Worksome\FeatureFlags\Contracts\FeatureFlagEnum
{
    case FlagOne = 'flag-one';
}
```

## Usage in Blade

```php
@feature(\App\Enums\FeatureFlag::FlagOne)
    This is content under a feature flag.
@endfeature
```

## Providers

The provider is set using the `FEATURE_FLAGS_PROVIDER` environment variable, and each provider is configured in the
`providers` section of the [config file](config/feature-flags.php).

### OpenFeature

The `open_feature` provider adds support for [OpenFeature](https://openfeature.dev), a vendor-agnostic ecosystem for
feature flagging. OpenFeature providers are created by a resolver, which implements the
`Worksome\FeatureFlags\Providers\OpenFeature\Contracts\OpenFeatureResolver` contract.

By default, the [flagd](https://flagd.dev) resolver is used, which requires flagd v0.16.0 or later. To use it, follow
the steps below:

1. Install the flagd OpenFeature provider
    ```shell
    composer require open-feature/flagd-provider
    ```
2. Set the required environment variables
    ```dotenv
    FEATURE_FLAGS_PROVIDER=open_feature
    FLAGD_HOST=localhost
    FLAGD_PORT=8013
    FLAGD_SECURE=false
    FLAGD_TIMEOUT=1.0
    ```

To use a different [OpenFeature provider](https://openfeature.dev/ecosystem?instant_search%5BrefinementList%5D%5Btype%5D%5B0%5D=Provider&instant_search%5BrefinementList%5D%5BallTechnologies%5D%5B0%5D=PHP),
create a resolver that returns it, and add it to the `resolvers` in the config file. Any options other than
`resolver` are passed to the resolver.

```php
'resolvers' => [
    'custom' => [
        'resolver' => \App\FeatureFlags\CustomOpenFeatureResolver::class,
        'api_key' => env('CUSTOM_API_KEY'),
    ],
],
```

Then select it with the `OPEN_FEATURE_RESOLVER` environment variable:

```dotenv
OPEN_FEATURE_RESOLVER=custom
```

Flags are evaluated over the network, so results are cached for `cache_ttl` seconds. If the provider becomes
unavailable, all flags are disabled for `retry_after` seconds, rather than making a failing request for every flag.

## Changelog

Please see the [Releases](https://github.com/worksome/feature-flags/releases) for more information on what has changed recently.

## Testing

We pride ourselves on a thorough test suite and strict static analysis. You can run all of our checks via a composer script:

```bash
composer test
```

To make it incredibly easy to contribute, we also provide a docker-compose file that will spin up a container
with all the necessary dependencies installed. Assuming you have docker installed, just run:

```bash
docker-compose run --rm composer install # Only needed the first time
docker-compose run --rm composer test # Run tests and static analysis 
```

Support for XDebug is baked into the Docker image, you just need to configure the `XDEBUG_MODE` environment variable:

```bash
docker-compose run --rm -e XDEBUG_MODE=debug php
```

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lukas Juhas](https://github.com/lukasjuhas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
