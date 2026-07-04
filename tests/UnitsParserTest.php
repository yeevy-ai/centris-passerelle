<?php

use Yeevy\CentrisPasserelle\Parser\UnitsParser;

it('parses the synthetic units fixture', function () {
    $records = iterator_to_array(
        (new UnitsParser)->parseFile(__DIR__.'/fixtures/synthetic/units.txt'),
        false,
    );

    expect($records)->toHaveCount(1);

    $unit = $records[0];

    expect($unit->mlsNumber)->toBe('9999999')
        ->and($unit->unitNumber)->toBe(1)
        ->and($unit->typeCode)->toBe('PRINCIPAL')
        ->and($unit->roomsTotal)->toBe(10)
        ->and($unit->bedrooms)->toBe(3)
        ->and($unit->noteFr)->toBe('Accès au jardin')
        ->and($unit->livingArea)->toBe(1082.0)
        ->and($unit->livingAreaUnit)->toBe('PC');
});
