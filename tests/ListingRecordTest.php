<?php

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\ListingRecord;
use Yeevy\CentrisPasserelle\Enums\ListingStatus;

function syntheticRow(array $values = []): array
{
    $map = ColumnMap::listings();
    $row = array_fill(0, 160, null);

    $defaults = [
        'mls_number' => '9999999',
        'sale_price' => '499000',
        'listing_date' => '2026/01/15',
        'street_name' => 'Rue Exemple',
        'postal_code' => 'J0X0X0',
        'year_built' => '1990',
        'living_area' => '5000',
        'bedrooms' => '3',
        'inclusions_fr' => 'Réfrigérateur, cuisinière',
        'modified_at' => '2026/01/20 10:00:00',
        'status_code' => 'EV',
        'latitude' => '45.50000000',
        'longitude' => '-73.50000000',
        'description_fr' => 'Description FR de test.<br/>',
    ];

    foreach ([...$defaults, ...$values] as $field => $value) {
        $position = $map->position($field);

        if ($position !== null) {
            $row[$position] = $value;
        }
    }

    return $row;
}

it('builds a typed record from a raw row', function () {
    $record = ListingRecord::fromRow(syntheticRow(), ColumnMap::listings());

    expect($record->mlsNumber)->toBe('9999999')
        ->and($record->salePrice)->toBe(499000)
        ->and($record->listingDate?->format('Y-m-d'))->toBe('2026-01-15')
        ->and($record->streetName)->toBe('Rue Exemple')
        ->and($record->postalCode)->toBe('J0X0X0')
        ->and($record->yearBuilt)->toBe(1990)
        ->and($record->livingArea)->toBe(5000.0)
        ->and($record->bedrooms)->toBe(3)
        ->and($record->inclusionsFr)->toBe('Réfrigérateur, cuisinière')
        ->and($record->modifiedAt?->format('Y-m-d H:i:s'))->toBe('2026-01-20 10:00:00')
        ->and($record->status)->toBe(ListingStatus::Active)
        ->and($record->latitude)->toBe(45.5)
        ->and($record->longitude)->toBe(-73.5)
        ->and($record->descriptionFr)->toBe('Description FR de test.<br/>');
});

it('keeps empty fields null', function () {
    $record = ListingRecord::fromRow(syntheticRow(['sale_price' => null]), ColumnMap::listings());

    expect($record->salePrice)->toBeNull()
        ->and($record->brokerCode)->toBeNull()
        ->and($record->descriptionEn)->toBeNull();
});

it('keeps unknown status codes as raw code with null enum', function () {
    $record = ListingRecord::fromRow(syntheticRow(['status_code' => 'XX']), ColumnMap::listings());

    expect($record->statusCode)->toBe('XX')
        ->and($record->status)->toBeNull();
});

it('exposes the raw row for unmapped columns', function () {
    $row = syntheticRow();
    $record = ListingRecord::fromRow($row, ColumnMap::listings());

    expect($record->row)->toBe($row);
});

it('computes a stable dirty hash that changes with the row', function () {
    $map = ColumnMap::listings();
    $same = ListingRecord::fromRow(syntheticRow(), $map);
    $again = ListingRecord::fromRow(syntheticRow(), $map);
    $changed = ListingRecord::fromRow(syntheticRow(['sale_price' => '510000']), $map);

    expect($same->dirtyHash)->toBe($again->dirtyHash)
        ->and($changed->dirtyHash)->not->toBe($same->dirtyHash);
});

it('rejects rows without an MLS number', function () {
    ListingRecord::fromRow(syntheticRow(['mls_number' => null]), ColumnMap::listings());
})->throws(InvalidArgumentException::class);

it('turns malformed numbers and dates into null', function () {
    $record = ListingRecord::fromRow(
        syntheticRow(['sale_price' => 'N/A', 'listing_date' => 'not a date']),
        ColumnMap::listings(),
    );

    expect($record->salePrice)->toBeNull()
        ->and($record->listingDate)->toBeNull();
});
