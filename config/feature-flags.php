<?php

declare(strict_types=1);

use Worksome\FeatureFlags\ModelFeatureFlagConvertor;

// config for Worksome/FeatureFlags
return [
    'default' => env('FEATURE_FLAGS_PROVIDER', 'launchdarkly'),

    /**
     * Convertor implementing FeatureFlagUserConvertor contract
     */
    'convertor' => ModelFeatureFlagConvertor::class,

    /**
     * Overrides implementing FeatureFlagOverrider contract
     */
    'overrider' => 'config',

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
     * Key is to be used to specify which overrider should be active
     *
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
                //
            ],
        ],
        'in-memory' => [
            //
        ]
    ],

];
