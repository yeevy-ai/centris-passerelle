<?php

use Yeevy\CentrisPasserelle\Parser\AdditionalLinksParser;

it('parses the synthetic additional links fixture', function () {
    $records = iterator_to_array(
        (new AdditionalLinksParser)->parseFile(__DIR__.'/fixtures/synthetic/additional-links.txt'),
        false,
    );

    expect($records)->toHaveCount(1);

    $link = $records[0];

    expect($link->mlsNumber)->toBe('9999999')
        ->and($link->sequence)->toBe(1)
        ->and($link->typeCode)->toBe('VIS3D')
        ->and($link->urlFr)->toBe('https://tour.test/fr/9999999')
        ->and($link->urlEn)->toBe('https://tour.test/en/9999999');
});
