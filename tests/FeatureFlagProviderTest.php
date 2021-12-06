<?php

use Worksome\FeatureFlags\Providers\FakeProvider;
use Illuminate\Support\Facades\Blade;

beforeEach(fn () => $this->provider = new FakeProvider());

it('should return false as a default value for a flag', function () {
    expect($this->provider->flag('non-existing-flag'))->toBe(false);
});

it('should return true if flag is set to true', function () {
    $this->provider->setFlag('test-flag', true);
    expect($this->provider->flag('test-flag'))->toBe(true);
});

it('should return false if flag is set to false', function () {
    $this->provider->setFlag('test-flag', false);
    expect($this->provider->flag('test-flag'))->toBe(false);
});

it('should successfully check inside a blade template', function () {
    $bladeSnippet = "@feature('test-flag') This is hidden feature @endfeature";
    $expectedCode = "<?php if (\Illuminate\Support\Facades\Blade::check('feature', 'test-flag')): ?> This is hidden feature <?php endif; ?>";
    expect(Blade::compileString($bladeSnippet))->toBe($expectedCode);
});
