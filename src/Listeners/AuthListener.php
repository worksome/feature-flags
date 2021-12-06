<?php

namespace Worksome\FeatureFlags\Listeners;

use Worksome\FeatureFlags\Facades\Feature;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Worksome\FeatureFlags\Contracts\FeatureFlagUserConvertor;

class AuthListener {
    public function handleUserAuth($event) {
        Feature::setUser(app(FeatureFlagUserConvertor::class)->convert($event->user));
    }

    public function handleUserLogout($event) {
        Feature::setAnonymousUser();
    }

    public function subscribe($events) {
        $events->listen(Authenticated::class, [self::class, 'handleUserAuth']);
        $events->listen(Login::class, [self::class, 'handleUserAuth']);
        $events->listen(Logout::class, [self::class, 'handleUserLogout']);
    }
}
