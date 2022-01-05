<?php

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
    'override-all' => env('FEATURE_FLAGS_ENABLE_ALL'),

    /**
     * Override flags. If a feature flag is set inside an override,
     * it will be used instead of the flag set in the provider.
     *
     * Usage: ['feature-flag-key' => true]
     */
    'overrides' => [],
];
