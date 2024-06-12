<?php

declare(strict_types=1);

namespace Worksome\FeatureFlags\Tests\Feature;

use Worksome\FeatureFlags\Overriders\InMemoryOverrider;
use Worksome\FeatureFlags\Tests\Enums\TestFeatureFlag;

beforeEach(function () {
    $this->inMemoryOverrider = new InMemoryOverrider();
});

test('has returns false if override key is not present', function () {
    expect($this->inMemoryOverrider->has(TestFeatureFlag::TestFlag))->toBeFalse();
});

test('has returns false if override key is present but null', function () {
    $this->inMemoryOverrider->set(TestFeatureFlag::TestFlag, null);
    expect($this->inMemoryOverrider->has(TestFeatureFlag::TestFlag))->toBeFalse();
});

test('has returns true if override key is present with truthy value', function ($value) {
    $this->inMemoryOverrider->set(TestFeatureFlag::TestFlag, $value);
    expect($this->inMemoryOverrider->has(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    true,
    1,
    1.0,
    'test',
    [1],
]);

test('has returns true if override key is present with falsy value', function ($value) {
    $this->inMemoryOverrider->set(TestFeatureFlag::TestFlag, $value);
    expect($this->inMemoryOverrider->has(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    false,
    0,
    0.0,
    '',
    '0',
    [[]],
]);

test('get returns true if override key is present with truthy value', function ($value) {
    $this->inMemoryOverrider->set(TestFeatureFlag::TestFlag, $value);
    expect($this->inMemoryOverrider->get(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    true,
    1,
    1.0,
    'test',
    [1],
]);

test('get returns false if override key is present with falsy value', function ($value) {
    $this->inMemoryOverrider->set(TestFeatureFlag::TestFlag, $value);
    expect($this->inMemoryOverrider->get(TestFeatureFlag::TestFlag))->toBeFalse();
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
    expect($this->inMemoryOverrider->get(TestFeatureFlag::TestFlag))->toBeFalse();
});

test('getAll returns true if override key is present with truthy value', function ($value) {
    $this->inMemoryOverrider->set(TestFeatureFlag::TestFlag, $value);
    expect($this->inMemoryOverrider->get(TestFeatureFlag::TestFlag))->toBeTrue();
})->with([
    true,
    1,
    1.0,
    'test',
    [1],
]);

test('getAll returns false if override key is present with falsy value', function ($value) {
    $this->inMemoryOverrider->set(TestFeatureFlag::TestFlag, $value);
    expect($this->inMemoryOverrider->get(TestFeatureFlag::TestFlag))->toBeFalse();
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
    expect($this->inMemoryOverrider->get(TestFeatureFlag::TestFlag))->toBeFalse();
});
