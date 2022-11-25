<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Worksome\FeatureFlags\FeatureFlagsOverrideProvider;
use Worksome\FeatureFlags\Overriders\ConfigOverrider;
use Worksome\FeatureFlags\Providers\FakeProvider;
use Worksome\FeatureFlags\Tests\Enums\TestFeatureFlag;

beforeEach(function () {
    $this->fakeProvider = new FakeProvider();
    $this->provider = new FeatureFlagsOverrideProvider($this->fakeProvider, new ConfigOverrider());
});

it('should return true if flag is set to true', function () {
    $this->fakeProvider->setFlag(TestFeatureFlag::TestFlag, true);
    expect($this->provider->flag(TestFeatureFlag::TestFlag))->toBeTrue();
});

it('should return false if flag is set to false', function () {
    $this->fakeProvider->setFlag(TestFeatureFlag::TestFlag, false);
    expect($this->provider->flag(TestFeatureFlag::TestFlag))->toBeFalse();
});

it('should validate the blade tags working correctly', function () {
    $bladeSnippet = "@feature('test-flag') This is hidden feature @endfeature";
    $expectedCode = "<?php if (\Illuminate\Support\Facades\Blade::check('feature', 'test-flag')): ?> This is hidden feature <?php endif; ?>";
    expect(Blade::compileString($bladeSnippet))->toBe($expectedCode);
});

it('should succesfully follow the override for a feature flag', function () {
    expect(Config::get('feature-flags.overrides.amazing-feature'))
        ->toBe(null)
        ->and($this->provider->flag(TestFeatureFlag::AmazingFeature))
        ->toBeFalse();

    Config::set('feature-flags.overrides.amazing-feature', true);

    expect(Config::get('feature-flags.overrides.amazing-feature'))
        ->toBeTrue()
        ->and($this->provider->flag(TestFeatureFlag::AmazingFeature))
        ->toBeTrue();
});

it('should correctly overide all feature flags if value is set', function () {
    $this->fakeProvider->setFlag(TestFeatureFlag::FlagOne, true);
    $this->fakeProvider->setFlag(TestFeatureFlag::FlagTwo, false);
    $this->fakeProvider->setFlag(TestFeatureFlag::FlagThree, false);


    expect($this->provider->flag(TestFeatureFlag::FlagOne))
        ->toBeTrue()
        ->and($this->provider->flag(TestFeatureFlag::FlagTwo))
        ->toBeFalse()
        ->and($this->provider->flag(TestFeatureFlag::FlagThree))
        ->toBeFalse();

    Config::set('feature-flags.override-all', true);

    expect($this->provider->flag(TestFeatureFlag::FlagOne))
        ->toBeTrue()
        ->and($this->provider->flag(TestFeatureFlag::FlagTwo))
        ->toBeTrue()
        ->and($this->provider->flag(TestFeatureFlag::FlagThree))
        ->toBeTrue();
});
