<?php

use Yeevy\CentrisPasserelle\Enums\ListingStatus;

it('maps feed status codes', function () {
    expect(ListingStatus::from('EV'))->toBe(ListingStatus::Active)
        ->and(ListingStatus::from('VE'))->toBe(ListingStatus::Sold);
});

it('returns null for unknown codes', function () {
    expect(ListingStatus::tryFrom('XX'))->toBeNull();
});
