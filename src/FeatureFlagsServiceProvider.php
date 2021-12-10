<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider as FeatureFlagsProviderContract;
use Illuminate\Support\Facades\Blade;
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
            __DIR__ . '/../config/feature-flags.php' => config_path('feature-flags.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/feature-flags.php',
            'feature-flags'
        );

        $this->app->extend(FeatureFlagsProviderContract::class, function ($provider, $app) {
            return $app->makeWith(FeatureFlagsOverrideProvider::class, [
                'provider' => $provider,
            ]);
        });

        $this->app->singleton(
            FeatureFlagsManager::class,
            static fn (Container $container) => new FeatureFlagsManager($container)
        );

        $this->app->singleton(
            FeatureFlagsProviderContract::class,
            function (Container $app) {
                /** @var FeatureFlagsManager $manager */
                $manager = $app->get(FeatureFlagsManager::class);
                return $manager->driver();
            }
        );

        $this->app->singleton(
            FeatureFlagUserConvertor::class,
            function (Container $app) {
                /** @var Application $app */
                $config = $app['config'];

                /** @var string */
                $convertor = $config->get('feature-flags.convertor');
                return $app->get($convertor);
            }
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
        Blade::if('feature', function (string $flag) {
            return Feature::flag($flag);
        });
    }
}
