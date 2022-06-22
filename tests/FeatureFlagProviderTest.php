<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Worksome\FeatureFlags\Overriders\ConfigOverrider;
use Worksome\FeatureFlags\FeatureFlagsOverrideProvider;
use Worksome\FeatureFlags\Providers\FakeProvider;

beforeEach(function () {
    $this->fakeProvider = new FakeProvider();
    $this->provider = new FeatureFlagsOverrideProvider($this->fakeProvider, new ConfigOverrider());
});

it('should return false as a default value for a flag', function () {
    expect($this->provider->flag('non-existing-flag'))->toBeFalse();
});

it('should return true if flag is set to true', function () {
    $this->fakeProvider->setFlag('test-flag', true);
    expect($this->provider->flag('test-flag'))->toBeTrue();
});

it('should return false if flag is set to false', function () {
    $this->fakeProvider->setFlag('test-flag', false);
    expect($this->provider->flag('test-flag'))->toBeFalse();
});

it('should validate the blade tags working correctly', function () {
    $bladeSnippet = "@feature('test-flag') This is hidden feature @endfeature";
    $expectedCode = "<?php if (\Illuminate\Support\Facades\Blade::check('feature', 'test-flag')): ?> This is hidden feature <?php endif; ?>";
    expect(Blade::compileString($bladeSnippet))->toBe($expectedCode);
});

it('should succesfully follow the override for a feature flag', function () {
    expect(Config::get('feature-flags.overrides.amazing-feature'))
        ->toBe(null)
        ->and($this->provider->flag('amazing-feature'))
        ->toBeFalse();

    Config::set('feature-flags.overrides.amazing-feature', true);

    expect(Config::get('feature-flags.overrides.amazing-feature'))
        ->toBeTrue()
        ->and($this->provider->flag('amazing-feature'))
        ->toBeTrue();
});

it('should correctly overide all feature flags if value is set', function () {
    $this->fakeProvider->setFlag('flag-1', true);
    $this->fakeProvider->setFlag('flag-2', false);
    $this->fakeProvider->setFlag('flag-3', false);


    expect($this->provider->flag('flag-1'))
        ->toBeTrue()
        ->and($this->provider->flag('flag-2'))
        ->toBeFalse()
        ->and($this->provider->flag('flag-3'))
        ->toBeFalse();

    Config::set('feature-flags.override-all', true);

    expect($this->provider->flag('flag-1'))
        ->toBeTrue()
        ->and($this->provider->flag('flag-2'))
        ->toBeTrue()
        ->and($this->provider->flag('flag-3'))
        ->toBeTrue();
});
