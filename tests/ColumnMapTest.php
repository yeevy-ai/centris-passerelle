<?php

use Yeevy\CentrisPasserelle\Config\ColumnMap;

it('loads the shipped listings map', function () {
    $map = ColumnMap::listings();

    expect($map->position('mls_number'))->toBe(0)
        ->and($map->position('status_code'))->toBe(115)
        ->and($map->position('unknown_field'))->toBeNull();
});

it('reads values by field name', function () {
    $map = new ColumnMap(['mls_number' => 0, 'sale_price' => 2]);
    $row = ['9999999', null, '499000'];

    expect($map->value($row, 'mls_number'))->toBe('9999999')
        ->and($map->value($row, 'sale_price'))->toBe('499000');
});

it('returns null for empty, missing and unmapped fields', function () {
    $map = new ColumnMap(['a' => 0, 'b' => 1, 'c' => 9]);
    $row = ['  ', null];

    expect($map->value($row, 'a'))->toBeNull()
        ->and($map->value($row, 'b'))->toBeNull()
        ->and($map->value($row, 'c'))->toBeNull()
        ->and($map->value($row, 'unmapped'))->toBeNull();
});

it('merges overrides without touching other positions', function () {
    $map = ColumnMap::listings()->with(['status_code' => 120]);

    expect($map->position('status_code'))->toBe(120)
        ->and($map->position('mls_number'))->toBe(0);
});

it('rejects invalid map files', function () {
    ColumnMap::fromFile('/nonexistent/map.php');
})->throws(InvalidArgumentException::class);

it('loads the shipped secondary-file maps', function () {
    expect(ColumnMap::remarks()->position('text'))->toBe(6)
        ->and(ColumnMap::addenda()->position('part_number'))->toBe(3)
        ->and(ColumnMap::photos()->position('url'))->toBe(6);
});

it('rejects unknown listings profiles', function () {
    ColumnMap::listings('2099');
})->throws(InvalidArgumentException::class, 'not found');

it('rejects malformed profile names', function () {
    ColumnMap::listings('../evil');
})->throws(InvalidArgumentException::class, 'Invalid column map profile name');
