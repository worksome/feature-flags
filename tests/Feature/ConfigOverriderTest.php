<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Tests;

use Illuminate\Config\Repository;
use Worksome\FeatureFlags\Overriders\ConfigOverrider;
use Worksome\FeatureFlags\Tests\Enums\TestFeatureFlag;

beforeEach(function () {
    $this->configRepo = $this->app->make(Repository::class);
    $this->configOverrides = new ConfigOverrider($this->configRepo);
});

test('has returns false if override key is not present', function () {
    expect($this->configOverrides->has(TestFeatureFlag::TestFlag))->toBeFalse();
});

test('has returns false if override key is present but null', function () {
    $this->configRepo->set('feature-flags.overriders.config.overrides.test', null);
    expect($this->configOverrides->has(TestFeatureFlag::TestFlag))->toBeFalse();
});

test('has returns true if override key is present with truthy value', function ($value) {
    $this->configRepo->set('feature-flags.overriders.config.overrides.test', $value);
    expect($this->configOverrides->has(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    true,
    1,
    1.0,
    'test',
    [1],
]);

test('has returns true if override key is present with falsy value', function ($value) {
    $this->configRepo->set('feature-flags.overriders.config.overrides.test', $value);
    expect($this->configOverrides->has(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    false,
    0,
    0.0,
    '',
    '0',
    [[]],
]);

test('get returns true if override key is present with truthy value', function ($value) {
    $this->configRepo->set('feature-flags.overriders.config.overrides.test', $value);
    expect($this->configOverrides->get(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    true,
    1,
    1.0,
    'test',
    [1],
]);

test('get returns false if override key is present with falsy value', function ($value) {
    $this->configRepo->set('feature-flags.overriders.config.overrides.test', $value);
    expect($this->configOverrides->get(TestFeatureFlag::TestFlag))->toBeFalse();
})->with([
    null,
    false,
    0,
    0.0,
    '',
    '0',
    [[]],
]);

test('get returns false if override key is not present', function () {
    expect($this->configOverrides->get(TestFeatureFlag::TestFlag))->toBeFalse();
});

test('getAll returns true if override key is present with truthy value', function ($value) {
    $this->configRepo->set('feature-flags.overriders.config.overrides.test', $value);
    expect($this->configOverrides->get(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    true,
    1,
    1.0,
    'test',
    [1],
]);

test('getAll returns false if override key is present with falsy value', function ($value) {
    $this->configRepo->set('feature-flags.overriders.config.overrides.test', $value);
    expect($this->configOverrides->get(TestFeatureFlag::TestFlag))->toBeFalse();
})->with([
    null,
    false,
    0,
    0.0,
    '',
    '0',
    [[]],
]);

test('getAll returns false if override key is not present', function () {
    expect($this->configOverrides->get(TestFeatureFlag::TestFlag))->toBeFalse();
});

it('sets override value for a single feature flag', function ($value) {
    $overrider = $this->app->make(ConfigOverrider::class);
    $overrider->set(TestFeatureFlag::TestFlag, $value);
    expect($this->configRepo->get('feature-flags.overriders.config.overrides.test'))->toBe($value);
})->with([
    true,
    false,
    null,
]);

it('sets override-all value', function ($value) {
    $overrider = $this->app->make(ConfigOverrider::class);
    $overrider->setAll($value);
    expect($this->configRepo->get('feature-flags.overriders.config.override-all'))->toBe($value);
})->with([
    true,
    false,
    null,
]);
