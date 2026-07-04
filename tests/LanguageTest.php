<?php

use Yeevy\CentrisPasserelle\Enums\Language;

it('maps feed language codes', function () {
    expect(Language::from('F'))->toBe(Language::French)
        ->and(Language::from('A'))->toBe(Language::English)
        ->and(Language::tryFrom('X'))->toBeNull();
});
