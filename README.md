# Feature Flags

[![PHPStan](https://github.com/worksome/feature-flags/actions/workflows/phpstan.yml/badge.svg)](https://github.com/worksome/feature-flags/actions/workflows/phpstan.yml)
[![Run Tests](https://github.com/worksome/feature-flags/actions/workflows/run-tests.yml/badge.svg)](https://github.com/worksome/feature-flags/actions/workflows/run-tests.yml)

A package to manage feature flags in your application. Currently supporting these providers:

- [LaunchDarkly](https://launchdarkly.com)
- [Reflag](https://reflag.com)

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
