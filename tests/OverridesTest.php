<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Tests;

use Illuminate\Config\Repository;
use Worksome\FeatureFlags\Overriders\ConfigOverrider;

beforeEach(function () {
    $this->configRepo = $this->app->make(Repository::class);
    $this->configOverrides = new ConfigOverrider();
});

test('has returns false if override key is not present', function () {
    expect($this->configOverrides->has('test'))->toBeFalse();
});

test('has returns false if override key is present but null', function () {
    $this->configRepo->set('feature-flags.overrides.test', null);
    expect($this->configOverrides->has('test'))->toBeFalse();
});

test('has returns true if override key is present with truthy value', function ($value) {
    $this->configRepo->set('feature-flags.overrides.test', $value);
    expect($this->configOverrides->has('test'))->toBeTrue();
})->with([
             true,
             1,
             1.0,
             "test",
             [1],
         ]);

test('has returns true if override key is present with falsy value', function ($value) {
    $this->configRepo->set('feature-flags.overrides.test', $value);
    expect($this->configOverrides->has('test'))->toBeTrue();
})->with([
             false,
             0,
             0.0,
             "",
             "0",
             [[]],
         ]);

test('get returns true if override key is present with truthy value', function ($value) {
    $this->configRepo->set('feature-flags.overrides.test', $value);
    expect($this->configOverrides->get('test'))->toBeTrue();
})->with([
             true,
             1,
             1.0,
             'test',
             [1],
         ]);

test('get returns false if override key is present with falsy value', function ($value) {
    $this->configRepo->set('feature-flags.overrides.test', $value);
    expect($this->configOverrides->get('test'))->toBeFalse();
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
    expect($this->configOverrides->get('test'))->toBeFalse();
});

test('getAll returns true if override key is present with truthy value', function ($value) {
    $this->configRepo->set('feature-flags.overrides.test', $value);
    expect($this->configOverrides->get('test'))->toBeTrue();
})->with([
             true,
             1,
             1.0,
             'test',
             [1],
         ]);

test('getAll returns false if override key is present with falsy value', function ($value) {
    $this->configRepo->set('feature-flags.overrides.test', $value);
    expect($this->configOverrides->get('test'))->toBeFalse();
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
    expect($this->configOverrides->get('test'))->toBeFalse();
});
