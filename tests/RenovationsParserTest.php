<?php

use Yeevy\CentrisPasserelle\Parser\RenovationsParser;

it('parses the synthetic renovations fixture', function () {
    $records = iterator_to_array(
        (new RenovationsParser)->parseFile(__DIR__.'/fixtures/synthetic/renovations.txt'),
        false,
    );

    expect($records)->toHaveCount(1);

    $renovation = $records[0];

    expect($renovation->mlsNumber)->toBe('9999999')
        ->and($renovation->typeCode)->toBe('CUI')
        ->and($renovation->year)->toBe(2025)
        ->and($renovation->descriptionFr)->toBe('Cuisine rénovée')
        ->and($renovation->amount)->toBe(25000);
});
