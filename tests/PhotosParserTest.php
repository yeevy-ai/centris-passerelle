<?php

use Yeevy\CentrisPasserelle\Parser\PhotosParser;

it('parses the synthetic photos fixture', function () {
    $records = iterator_to_array(
        (new PhotosParser)->parseFile(__DIR__.'/fixtures/synthetic/photos.txt'),
        false,
    );

    expect($records)->toHaveCount(2);

    [$facade, $kitchen] = $records;

    expect($facade->mlsNumber)->toBe('9999999')
        ->and($facade->sequence)->toBe(1)
        ->and($facade->categoryCode)->toBe('FACA')
        ->and($facade->descriptionFr)->toBe('Façade avant')
        ->and($facade->descriptionEn)->toBe('Front façade')
        ->and($facade->url)->toBe('https://mediaserver.centris.ca/media.ashx?id=TEST0001')
        ->and($facade->photoId)->toBe('100000001')
        ->and($facade->modifiedAt?->format('Y-m-d H:i:s'))->toBe('2026-01-20 10:00:00')
        ->and($kitchen->sequence)->toBe(2)
        ->and($kitchen->categoryCode)->toBe('CUI');
});

it('skips photo rows without an MLS number', function () {
    $records = iterator_to_array(
        (new PhotosParser)->parseString(",1,,\"FACA\",,,\"https://example.test/a\",\"1\",\"2026/01/20 10:00:00\"\r\n"),
        false,
    );

    expect($records)->toHaveCount(0);
});
