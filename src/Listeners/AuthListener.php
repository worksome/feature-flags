<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Listeners;

use Worksome\FeatureFlags\Facades\Feature;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Worksome\FeatureFlags\Contracts\FeatureFlagUserConvertor;

class AuthListener
{
    public function __construct(
        private FeatureFlagUserConvertor $convertor,
    ) {
    }

    public function handleUserAuth(Authenticated|Login $event): void
    {
        Feature::setUser($this->convertor->convert($event->user));
    }

    public function handleUserLogout(Logout $event): void
    {
        Feature::setAnonymousUser();
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Authenticated::class, [self::class, 'handleUserAuth']);
        $events->listen(Login::class, [self::class, 'handleUserAuth']);
        $events->listen(Logout::class, [self::class, 'handleUserLogout']);
    }
}
