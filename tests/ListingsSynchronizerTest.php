<?php

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Events\ListingCreated;
use Yeevy\CentrisPasserelle\Events\ListingRemoved;
use Yeevy\CentrisPasserelle\Events\ListingUpdated;
use Yeevy\CentrisPasserelle\Exceptions\ColumnMapMismatch;
use Yeevy\CentrisPasserelle\Sync\ListingsSynchronizer;
use Yeevy\CentrisPasserelle\Tests\Support\CollectingDispatcher;
use Yeevy\CentrisPasserelle\Tests\Support\InMemoryListingRepository;
use Yeevy\CentrisPasserelle\Validation\SnapshotValidator;

const SNAPSHOT_DIR = __DIR__.'/fixtures/synthetic';
const SNAPSHOT_FILE = SNAPSHOT_DIR.'/listings.txt';

it('creates unknown listings and dispatches ListingCreated', function () {
    $repository = new InMemoryListingRepository;
    $events = new CollectingDispatcher;

    $result = (new ListingsSynchronizer($repository, events: $events))->sync(SNAPSHOT_FILE);

    expect($result->created)->toBe(1)
        ->and($result->updated)->toBe(0)
        ->and($result->skipped)->toBe(0)
        ->and($result->removed)->toBe(0)
        ->and($result->total())->toBe(1)
        ->and($repository->saved)->toHaveKey('9999999')
        ->and($events->events)->toHaveCount(1)
        ->and($events->events[0])->toBeInstanceOf(ListingCreated::class);
});

it('skips unchanged rows on a re-sync', function () {
    $repository = new InMemoryListingRepository;
    $synchronizer = new ListingsSynchronizer($repository);

    $synchronizer->sync(SNAPSHOT_FILE);
    $result = $synchronizer->sync(SNAPSHOT_FILE);

    expect($result->created)->toBe(0)
        ->and($result->skipped)->toBe(1)
        ->and($result->removed)->toBe(0);
});

it('updates listings whose stored hash differs and dispatches ListingUpdated', function () {
    $repository = new InMemoryListingRepository;
    $repository->hashes['9999999'] = 'stale-hash';
    $events = new CollectingDispatcher;

    $result = (new ListingsSynchronizer($repository, events: $events))->sync(SNAPSHOT_FILE);

    expect($result->updated)->toBe(1)
        ->and($result->created)->toBe(0)
        ->and($events->events[0])->toBeInstanceOf(ListingUpdated::class);
});

it('removes listings absent from the snapshot and dispatches ListingRemoved', function () {
    $repository = new InMemoryListingRepository;
    $repository->hashes['1111111'] = 'some-hash'; // in storage, not in snapshot
    $events = new CollectingDispatcher;

    $result = (new ListingsSynchronizer($repository, events: $events))->sync(SNAPSHOT_FILE);

    expect($result->removed)->toBe(1)
        ->and($repository->removed)->toBe(['1111111'])
        ->and($repository->hashes)->toHaveKey('9999999');

    $removals = array_values(array_filter($events->events, fn ($e) => $e instanceof ListingRemoved));

    expect($removals)->toHaveCount(1)
        ->and($removals[0]->mlsNumber)->toBe('1111111');
});

it('accepts a snapshot directory with a custom filename', function () {
    $repository = new InMemoryListingRepository;

    $result = (new ListingsSynchronizer($repository))->sync(SNAPSHOT_DIR, 'listings.txt');

    expect($result->created)->toBe(1);
});

it('writes nothing when validation fails', function () {
    $repository = new InMemoryListingRepository;
    $shifted = new SnapshotValidator(ColumnMap::listings()->with(['mls_number' => 27]));
    $synchronizer = new ListingsSynchronizer($repository, validator: $shifted);

    try {
        $synchronizer->sync(SNAPSHOT_FILE);
        $this->fail('Expected ColumnMapMismatch');
    } catch (ColumnMapMismatch) {
        // expected
    }

    expect($repository->saved)->toBe([])
        ->and($repository->removed)->toBe([]);
});
