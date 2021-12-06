<?php

namespace Worksome\FeatureFlags;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider as FeatureFlagsProviderContract;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Worksome\FeatureFlags\Contracts\FeatureFlagUserConvertor;
use Worksome\FeatureFlags\Facades\Feature;
use Worksome\FeatureFlags\Listeners\AuthListener;

class FeatureFlagsServiceProvider extends EventServiceProvider
{
    protected $subscribe = [
        AuthListener::class
    ];

    public function boot(): void
    {
        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }

        $this->publishes([
            __DIR__.'/../config/feature-flags.php' => config_path('feature-flags.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/feature-flags.php', 'feature-flags'
        );
    }

    public function register(): void
    {
        $this->app->singleton(
            FeatureFlagsManager::class,
            static fn(Container $container) => new FeatureFlagsManager($container)
        );

        $this->app->singleton(
            FeatureFlagsProviderContract::class,
            static fn(Container $app) => $app->get(FeatureFlagsManager::class)->driver()
        );

        $this->app->singleton(
            FeatureFlagUserConvertor::class,
            static fn(Container $app) => $app->get(Config::get('feature-flags.convertor'))
        );

        $this->registerBlade();
    }

    public function provides(): array
    {
        return [
            FeatureFlagsProviderContract::class,
            FeatureFlagsManager::class,
            FeatureFlagUserConvertor::class
        ];
    }

    private function registerBlade(): void
    {
        Blade::if('feature', function(string $flag) {
            return Feature::flag($flag);
        });
    }
}
