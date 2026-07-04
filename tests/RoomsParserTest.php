<?php

use Yeevy\CentrisPasserelle\Parser\RoomsParser;

it('parses the synthetic rooms fixture', function () {
    $records = iterator_to_array(
        (new RoomsParser)->parseFile(__DIR__.'/fixtures/synthetic/rooms.txt'),
        false,
    );

    expect($records)->toHaveCount(2);

    [$livingRoom, $bedroom] = $records;

    expect($livingRoom->typeCode)->toBe('SAL')
        ->and($livingRoom->levelCode)->toBe('RC')
        ->and($livingRoom->dimensions)->toBe('15.6x12.2 P')
        ->and($livingRoom->flooringCode)->toBe('BOIS')
        ->and($livingRoom->descriptionFr)->toBe('foyer au bois')
        ->and($bedroom->unitNumber)->toBe(1)
        ->and($bedroom->sequence)->toBe(2)
        ->and($bedroom->nameFr)->toBe('Chambre des maîtres')
        ->and($bedroom->nameEn)->toBe('Primary bedroom')
        ->and($bedroom->flooringFr)->toBe('moquette');
});
