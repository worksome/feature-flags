<?php

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
     * Override flags. If a feature flag is set inside an override,
     * it will be used instead of the flag set in the provider.
     *
     * Usage: ['feature-flag-key' => true]
     */
    'overrides' => [],
];
