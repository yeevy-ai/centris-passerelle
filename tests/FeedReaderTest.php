<?php

use Yeevy\CentrisPasserelle\Support\FeedReader;

it('opens feed files as UTF-8 readers', function () {
    $reader = FeedReader::fromFile(__DIR__.'/fixtures/synthetic/listings.txt');

    $rows = iterator_to_array($reader->getRecords(), false);

    expect($rows)->toHaveCount(1)
        ->and($rows[0][100])->toBe('Réfrigérateur, cuisinière');
});

it('opens raw feed bytes as UTF-8 readers', function () {
    // é as the Windows-1252 single byte 0xE9, as delivered by the feed
    $reader = FeedReader::fromString("\"9999999\",\"R\xE9frig\xE9rateur\"\r\n");

    $rows = iterator_to_array($reader->getRecords(), false);

    expect($rows[0][1])->toBe('Réfrigérateur');
});
