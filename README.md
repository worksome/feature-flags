# Feature Flags

[![PHPStan](https://github.com/worksome/feature-flags/actions/workflows/phpstan.yml/badge.svg)](https://github.com/worksome/feature-flags/actions/workflows/phpstan.yml)
[![Run Tests](https://github.com/worksome/feature-flags/actions/workflows/run-tests.yml/badge.svg)](https://github.com/worksome/feature-flags/actions/workflows/run-tests.yml)

A package to manage feature flags in your application. Currently supporting these providers:

- [LaunchDarkly](https://launchdarkly.com/)

## Installation

You can install the package via composer:

```bash
composer require worksome/feature-flags
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="feature-flags-config"
```

This is the contents of the published config file:

```php
declare(strict_types=1);

use Worksome\FeatureFlags\ModelFeatureFlagConvertor;

// config for Worksome/FeatureFlags
return [
    'default' => env('FEATURE_FLAGS_PROVIDER', 'launchdarkly'),

    'convertor' => ModelFeatureFlagConvertor::class,

    'providers' => [
        'launchdarkly' => [
            'key' => env('LAUNCHDARKLY_SDK_KEY'),
            'options' => [
                /**
                 * https://docs.launchdarkly.com/sdk/features/offline-mode
                 */
                'offline' => env('LAUNCHDARKLY_OFFLINE', false)
            ],
            /**
             * @link https://docs.launchdarkly.com/home/account-security/api-access-tokens
             */
            'access-token' => env('FEATURE_FLAGS_API_ACCESS_TOKEN', null),
        ]
    ],

    /**
     * List of available overriders.
     * Key is to be used to specify which overrider should be active.
     */
    'overriders' => [
        'config' => [
            /**
             * Overrides all feature flags directly without hitting the provider.
             * This is particularly useful for running things in the CI,
             * e.g. Cypress tests.
             *
             * Be careful in setting a default value as said value will be applied to all flags.
             * Use `null` value if needing the key to be present but act as if it was not
             */
            'override-all' => null,

            /**
             * Override flags. If a feature flag is set inside an override,
             * it will be used instead of the flag set in the provider.
             *
             * Usage: ['feature-flag-key' => true]
             *
             * Be careful in setting a default value as it will be applied.
             * Use `null` value if needing the key to be present but act as if it was not
             *
             */
            'overrides' => [
                // ...
            ],
        ],
        'in-memory' => [
            // ...
        ]
    ],
];
```

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
