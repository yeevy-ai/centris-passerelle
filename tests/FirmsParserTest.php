<?php

use Yeevy\CentrisPasserelle\Parser\FirmsParser;

it('parses the synthetic firms fixture', function () {
    $records = iterator_to_array(
        (new FirmsParser)->parseFile(__DIR__.'/fixtures/synthetic/firms.txt'),
        false,
    );

    expect($records)->toHaveCount(1);

    $firm = $records[0];

    expect($firm->firmCode)->toBe('XXX')
        ->and($firm->name)->toBe('AGENCE EXEMPLE INC.')
        ->and($firm->licenseNumber)->toBe('G0000')
        ->and($firm->bannerCode)->toBe('IND')
        ->and($firm->firmNumber)->toBe(100000);
});
