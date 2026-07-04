<?php

use Yeevy\CentrisPasserelle\Parser\OpenHousesParser;

it('parses the synthetic open houses fixture', function () {
    $records = iterator_to_array(
        (new OpenHousesParser)->parseFile(__DIR__.'/fixtures/synthetic/open-houses.txt'),
        false,
    );

    expect($records)->toHaveCount(1);

    $openHouse = $records[0];

    expect($openHouse->mlsNumber)->toBe('9999999')
        ->and($openHouse->startDate?->format('Y-m-d'))->toBe('2026-07-05')
        ->and($openHouse->startTime)->toBe('14:00')
        ->and($openHouse->endTime)->toBe('16:00')
        ->and($openHouse->typeCode)->toBe('VL');
});
