<?php

use Yeevy\CentrisPasserelle\Support\Cast;

it('casts numeric strings', function () {
    expect(Cast::toInt('499000'))->toBe(499000)
        ->and(Cast::toFloat('45.50000000'))->toBe(45.5);
});

it('turns malformed and missing values into null', function () {
    expect(Cast::toInt('N/A'))->toBeNull()
        ->and(Cast::toInt(null))->toBeNull()
        ->and(Cast::toFloat('12,00'))->toBeNull()
        ->and(Cast::toDate('not a date', '!Y/m/d'))->toBeNull()
        ->and(Cast::toDate(null, '!Y/m/d'))->toBeNull();
});

it('parses feed dates with a reset time component', function () {
    $date = Cast::toDate('2026/01/15', '!Y/m/d');

    expect($date?->format('Y-m-d H:i:s'))->toBe('2026-01-15 00:00:00');
});
