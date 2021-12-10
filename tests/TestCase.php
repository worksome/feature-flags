<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;
use Worksome\FeatureFlags\FeatureFlagsServiceProvider;
use Worksome\FeatureFlags\Providers\FakeProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Worksome\\FeatureFlags\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->app->singleton(
            FeatureFlagsProvider::class,
            static fn () => new FakeProvider()
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FeatureFlagsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // fake launchdarkly credentials
        config()->set('feature-flags.providers.launchdarkly.key', 'asldkngibjdnviunsdijbasdjbsaf');
    }
}
