<?php

use Yeevy\CentrisPasserelle\Parser\OfficesParser;

it('parses the synthetic offices fixture', function () {
    $records = iterator_to_array(
        (new OfficesParser)->parseFile(__DIR__.'/fixtures/synthetic/offices.txt'),
        false,
    );

    expect($records)->toHaveCount(1);

    $office = $records[0];

    expect($office->officeCode)->toBe('XXX001')
        ->and($office->firmCode)->toBe('XXX')
        ->and($office->name)->toBe('SIÈGE SOCIAL')
        ->and($office->streetName)->toBe('Rue Exemple')
        ->and($office->city)->toBe('Montréal')
        ->and($office->province)->toBe('QC')
        ->and($office->postalCode)->toBe('H0H0H0')
        ->and($office->fax)->toBe('5145550104')
        ->and($office->firmNumber)->toBe(100000);
});
