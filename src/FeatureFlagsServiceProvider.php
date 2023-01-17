<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Worksome\FeatureFlags\Contracts\FeatureFlagEnum;
use Worksome\FeatureFlags\Contracts\FeatureFlagOverrider;
use Worksome\FeatureFlags\Contracts\FeatureFlagsApiProvider as FeatureFlagsApiProviderContract;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider as FeatureFlagsProviderContract;
use Worksome\FeatureFlags\Contracts\FeatureFlagUserConvertor;
use Worksome\FeatureFlags\Listeners\AuthListener;

class FeatureFlagsServiceProvider extends EventServiceProvider
{
    protected $subscribe = [
        AuthListener::class
    ];

    public function boot(): void
    {
        $this->callAfterResolving(Dispatcher::class, function (Dispatcher $event) {
            foreach ($this->subscribe as $subscriber) {
                $event->subscribe($subscriber);
            }
        });

        $this->publishes([
            __DIR__ . '/../config/feature-flags.php' => $this->app->configPath('feature-flags.php'),
        ]);

        $this->registerBlade();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/feature-flags.php',
            'feature-flags'
        );

        $this->app->extend(FeatureFlagsProviderContract::class, function ($provider, Container $app) {
            return $app->makeWith(FeatureFlagsOverrideProvider::class, [
                'provider' => $provider,
            ]);
        });

        $this->app->singleton(
            FeatureFlagsManager::class,
            static fn (Container $container) => new FeatureFlagsManager($container)
        );

        $this->app->singleton(FeatureFlagsProviderContract::class, function (Container $app) {
            /** @var FeatureFlagsManager $manager */
            $manager = $app->get(FeatureFlagsManager::class);

            return $manager->driver();
        });

        $this->app->singleton(FeatureFlagUserConvertor::class, function (Container $app) {
            /** @var ConfigRepository $config */
            $config = $app->get('config');

            /** @var class-string<FeatureFlagUserConvertor> $convertor */
            $convertor = $config->get('feature-flags.convertor');

            return $app->get($convertor);
        });

        $this->app->singleton(
            FeatureFlagsApiProviderContract::class,
            function (Container $app) {
                /** @var FeatureFlagsApiManager $manager */
                $manager = $app->get(FeatureFlagsApiManager::class);
                return $manager->driver();
            }
        );

        $this->app->singleton(
            FeatureFlagOverrider::class,
            function (Container $app) {
                /** @var ConfigRepository $config */
                $config = $app->get('config');

                /** @var class-string<FeatureFlagOverrider> $convertor */
                $convertor = $config->get('feature-flags.overrider');

                return $app->get($convertor);
            }
        );
    }

    public function provides(): array
    {
        return [
            FeatureFlagsProviderContract::class,
            FeatureFlagsApiProviderContract::class,
            FeatureFlagOverrider::class,
            FeatureFlagsManager::class,
            FeatureFlagUserConvertor::class,
        ];
    }

    private function registerBlade(): void
    {
        $this->callAfterResolving(BladeCompiler::class, function (BladeCompiler $blade) {
            $blade->if('feature', function (FeatureFlagEnum $flag) {
                /** @var FeatureFlagsProviderContract $featureFlag */
                $featureFlag = $this->app->get(FeatureFlagsProviderContract::class);
                return $featureFlag->flag($flag);
            });
        });
    }
}
