<?php

use Yeevy\CentrisPasserelle\Parser\FeaturesParser;

it('parses the synthetic features fixture', function () {
    $records = iterator_to_array(
        (new FeaturesParser)->parseFile(__DIR__.'/fixtures/synthetic/features.txt'),
        false,
    );

    expect($records)->toHaveCount(2);

    [$water, $driveway] = $records;

    expect($water->mlsNumber)->toBe('9999999')
        ->and($water->categoryCode)->toBe('PART')
        ->and($water->featureCode)->toBe('EAU')
        ->and($water->valueFr)->toBe('Lac')
        ->and($water->valueEn)->toBe('Lake')
        ->and($driveway->categoryCode)->toBe('ALLE')
        ->and($driveway->quantity)->toBe(2)
        ->and($driveway->numericValue)->toBe(15.0);
});
