<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Providers\LaunchDarkly\Api;

use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Worksome\FeatureFlags\Contracts\FeatureFlagsApiProvider;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyMissingAccessTokenException;
use Worksome\FeatureFlags\Exceptions\LaunchDarkly\LaunchDarklyUnavailableException;

class LaunchDarklyApiProvider implements FeatureFlagsApiProvider
{
    public function __construct(
        private readonly string $environmentKey = 'testing',
        private readonly string $projectKey = 'default',
    ) {
        if (! Config::get('feature-flags.providers.launchdarkly.access-token')) {
            throw new LaunchDarklyMissingAccessTokenException();
        }
    }

    private function client(): GuzzleHttpClient
    {
        return new GuzzleHttpClient([
            'http_errors' => false,
            'base_uri' => 'https://app.launchdarkly.com',
        ]);
    }

    private function headers(): array
    {
        return [
            'Authorization' => Config::get('feature-flags.providers.launchdarkly.access-token'),
            'Content-Type' => 'application/json; domain-model=launchdarkly.semanticpatch'
        ];
    }

    public function isAccessTokenValid(): bool
    {
        try {
            $request = $this->client()->get(
                '/api/v2/tokens',
                [
                    RequestOptions::HEADERS => $this->headers(),
                ]
            );

            return ! empty($request->getBody()->getContents());
        } catch (Throwable) {
            throw new LaunchDarklyUnavailableException();
        }
    }

    public function get(FeatureFlagEnum $featureFlagKey): ResponseInterface
    {
        try {
            return $this->client()->get(
                sprintf('/api/v2/flags/%s/%s', $this->projectKey, $featureFlagKey->value),
                [
                    RequestOptions::HEADERS => $this->headers(),
                    RequestOptions::JSON => [
                            "environmentKey" => $this->environmentKey,
                    ],
                ]
            );
        } catch (Throwable) {
            throw new LaunchDarklyUnavailableException();
        }
    }

    private function getVariationId(FeatureFlagEnum $featureFlagKey, bool $variationValue): ?string
    {
        try {
            $featureFlag = $this->get($featureFlagKey);
            /** @var array */
            $featureFlag = json_decode($featureFlag->getBody()->getcontents(), true);

            if (isset($featureFlag['variations'])) {
                /** @var array<string> */
                $values = array_column($featureFlag['variations'], '_id', 'value');
                return $values[$variationValue];
            }

            return null;
        } catch (Throwable) {
            throw new LaunchDarklyUnavailableException();
        }
    }

    private function enableOrDisableInstruction(
        FeatureFlagEnum $featureFlagKey,
        string $instruction,
        string $comment = 'Updated via the API.',
    ): ResponseInterface {
        try {
            return $this->client()->patch(
                sprintf('/api/v2/flags/%s/%s', $this->projectKey, $featureFlagKey->value),
                [
                    RequestOptions::HEADERS => $this->headers(),
                    RequestOptions::JSON => [
                            "environmentKey" => $this->environmentKey,
                            "comment" => $comment,
                            "instructions" => [
                                [
                                    "kind" => $instruction,
                                ]
                            ]
                    ],
                ]
            );
        } catch (Throwable) {
            throw new LaunchDarklyUnavailableException();
        }
    }

    public function clearRules(
        FeatureFlagEnum $featureFlagKey,
        string $comment = 'Updated via the API.',
    ): ResponseInterface {
        try {
            return $this->client()->patch(
                sprintf('/api/v2/flags/%s/%s', $this->projectKey, $featureFlagKey->value),
                [
                    RequestOptions::HEADERS => $this->headers(),
                    RequestOptions::JSON => [
                            "environmentKey" => $this->environmentKey,
                            "comment" => $comment,
                            "instructions" => [
                                [
                                    "kind" => 'replaceRules',
                                    "rules" => [],
                                ]
                            ]
                    ],
                ]
            );
        } catch (Throwable) {
            throw new LaunchDarklyUnavailableException();
        }
    }

    /**
     * Clears all previous rules and creates a new rule
     */
    public function addRuleForEmailAddresses(
        FeatureFlagEnum $featureFlagKey,
        bool $featureFlagValue,
        array $emailAddresses,
        string $comment = 'Updated via the API.',
    ): ResponseInterface {
        try {
            // clear all previous rules
            $this->clearRules($featureFlagKey, $comment);

            // get variation id
            $variationId = $this->getVariationId($featureFlagKey, $featureFlagValue);

            // add the rule
            return $this->client()->patch(
                sprintf('/api/v2/flags/%s/%s', $this->projectKey, $featureFlagKey->value),
                [
                    RequestOptions::HEADERS => $this->headers(),
                    RequestOptions::JSON => [
                        "environmentKey" => $this->environmentKey,
                        "comment" => $comment,
                        "instructions" => [
                            [
                                "kind" => "addRule",
                                "clauses" => [
                                    [
                                        "attribute" => "email",
                                        "op" => "in",
                                        "negate" => false,
                                        "values" => $emailAddresses
                                    ]
                                ],
                                "variationId" => $variationId,
                            ]
                        ],
                    ],
                ]
            );
        } catch (Throwable) {
            throw new LaunchDarklyUnavailableException();
        }
    }

    public function enable(FeatureFlagEnum $featureFlagKey, string $comment = ''): ResponseInterface
    {
        return $this->enableOrDisableInstruction($featureFlagKey, 'turnFlagOn', $comment);
    }

    public function disable(FeatureFlagEnum $featureFlagKey, string $comment = ''): ResponseInterface
    {
        return $this->enableOrDisableInstruction($featureFlagKey, 'turnFlagOff', $comment);
    }
}
