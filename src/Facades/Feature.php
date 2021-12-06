<?php

namespace Worksome\FeatureFlags\Facades;

use Illuminate\Support\Facades\Facade;
use Worksome\FeatureFlags\Contracts\FeatureFlagsProvider;

class Feature extends Facade {
    protected static function getFacadeAccessor() {
        return FeatureFlagsProvider::class;
    }
}
