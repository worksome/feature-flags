<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Worksome\FeatureFlags\FeatureFlagsOverridesRepository;
use Worksome\FeatureFlags\FeatureFlagsOverrideProvider;
use Worksome\FeatureFlags\Providers\FakeProvider;

beforeEach(function () {
    $this->fakeProvider = new FakeProvider();
    $this->provider = new FeatureFlagsOverrideProvider($this->fakeProvider, new FeatureFlagsOverridesRepository());
});

it('should return false as a default value for a flag', function () {
    expect($this->provider->flag('non-existing-flag'))->toBe(false);
});

it('should return true if flag is set to true', function () {
    $this->fakeProvider->setFlag('test-flag', true);
    expect($this->provider->flag('test-flag'))->toBe(true);
});

it('should return false if flag is set to false', function () {
    $this->fakeProvider->setFlag('test-flag', false);
    expect($this->provider->flag('test-flag'))->toBe(false);
});

it('should successfully check inside a blade template', function () {
    $bladeSnippet = "@feature('test-flag') This is hidden feature @endfeature";
    $expectedCode = "<?php if (\Illuminate\Support\Facades\Blade::check('feature', 'test-flag')): ?> This is hidden feature <?php endif; ?>";
    expect(Blade::compileString($bladeSnippet))->toBe($expectedCode);
});

it('should succesfully follow override', function () {
    expect(Config::get('feature-flags.overrides.amazing-feature'))->toBe(null);
    expect($this->provider->flag('amazing-feature'))->toBe(false);

    Config::set('feature-flags.overrides.amazing-feature', true);

    expect(Config::get('feature-flags.overrides.amazing-feature'))->toBe(true);
    expect($this->provider->flag('amazing-feature'))->toBe(true);
});
