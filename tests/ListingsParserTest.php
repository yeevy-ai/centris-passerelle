<?php

use Psr\Log\AbstractLogger;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\ListingRecord;
use Yeevy\CentrisPasserelle\Enums\ListingStatus;
use Yeevy\CentrisPasserelle\Parser\ListingsParser;

const SYNTHETIC_FIXTURE = __DIR__.'/fixtures/synthetic/listings.txt';

it('parses the synthetic fixture into typed records', function () {
    $records = iterator_to_array((new ListingsParser)->parseFile(SYNTHETIC_FIXTURE), false);

    expect($records)->toHaveCount(1);

    $record = $records[0];

    expect($record)->toBeInstanceOf(ListingRecord::class)
        ->and($record->mlsNumber)->toBe('9999999')
        ->and($record->salePrice)->toBe(499000)
        ->and($record->streetName)->toBe('Rue Exemple')
        ->and($record->status)->toBe(ListingStatus::Active)
        ->and($record->latitude)->toBe(45.5)
        ->and($record->longitude)->toBe(-73.5)
        ->and($record->descriptionEn)->toBe('Test EN description.<br/>');
});

it('converts Windows-1252 content while parsing', function () {
    $records = iterator_to_array((new ListingsParser)->parseFile(SYNTHETIC_FIXTURE), false);

    expect($records[0]->inclusionsFr)->toBe('Réfrigérateur, cuisinière');
});

it('keeps quoted commas inside a single field', function () {
    $records = iterator_to_array((new ListingsParser)->parseFile(SYNTHETIC_FIXTURE), false);

    // "Réfrigérateur, cuisinière" and "Fridge, stove" are adjacent columns —
    // a naive explode(',') would shift every following position.
    expect($records[0]->inclusionsFr)->toBe('Réfrigérateur, cuisinière')
        ->and($records[0]->inclusionsEn)->toBe('Fridge, stove');
});

it('parses raw feed bytes from a string', function () {
    $contents = file_get_contents(SYNTHETIC_FIXTURE);

    expect($contents)->toBeString();

    $records = iterator_to_array((new ListingsParser)->parseString((string) $contents), false);

    expect($records)->toHaveCount(1)
        ->and($records[0]->mlsNumber)->toBe('9999999');
});

it('honours a custom column map', function () {
    $map = ColumnMap::listings()->with(['street_name' => 29]); // postal code position

    $records = iterator_to_array((new ListingsParser($map))->parseFile(SYNTHETIC_FIXTURE), false);

    expect($records[0]->streetName)->toBe('J0X0X0');
});

it('logs and skips rows without an MLS number', function () {
    $logger = new class extends AbstractLogger
    {
        /** @var list<string> */
        public array $messages = [];

        public function log($level, Stringable|string $message, array $context = []): void
        {
            $this->messages[] = "{$level}: {$message}";
        }
    };

    $contents = ",,\"no-mls\"\r\n\"1234567\",,\"00001\"\r\n";

    $records = iterator_to_array((new ListingsParser(logger: $logger))->parseString($contents), false);

    expect($records)->toHaveCount(1)
        ->and($records[0]->mlsNumber)->toBe('1234567')
        ->and($logger->messages)->toHaveCount(1)
        ->and($logger->messages[0])->toContain('warning');
});
