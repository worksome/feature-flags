# Feature Flags

[![PHPStan](https://github.com/worksome/feature-flags/actions/workflows/phpstan.yml/badge.svg)](https://github.com/worksome/feature-flags/actions/workflows/phpstan.yml)
[![run-tests](https://github.com/worksome/feature-flags/actions/workflows/run-tests.yml/badge.svg)](https://github.com/worksome/feature-flags/actions/workflows/run-tests.yml)

A package to manage feature flags in your application. Currently supporting these providers:

- [LaunchDarkly](https://launchdarkly.com/)

## Installation

You can install the package via composer:

```bash
composer require worksome/feature-flags
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="feature-flags_without_prefix-config"
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
            ]
        ]
    ],

    /**
     * Overrides all feature flags directly without hitting the provider.
     * This is particularly useful for running things in the CI,
     * e.g. Cypress tests.
     */
    'override-all' => env('FEATURE_FLAGS_OVERRIDE_ALL'),

    /**
     * Override flags. If a feature flag is set inside an override,
     * it will be used instead of the flag set in the provider.
     *
     * Usage: ['feature-flag-key' => true]
     */
    'overrides' => [],
];
```

## Usage in Blade

```php
@feature('feature-flag')
    This is content under a feature flag.
@endfeature
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lukas Juhas](https://github.com/lukasjuhas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
