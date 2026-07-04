<?php

use Psr\Log\AbstractLogger;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Exceptions\ColumnMapMismatch;
use Yeevy\CentrisPasserelle\Validation\SnapshotValidator;

it('accepts the synthetic fixture with the default map', function () {
    (new SnapshotValidator)->validateFile(__DIR__.'/fixtures/synthetic/listings.txt');

    expect(true)->toBeTrue();
});

it('throws when the map no longer lines up with the file', function () {
    // Pretend the MLS number lives where the street name actually is.
    $shifted = ColumnMap::listings()->with(['mls_number' => 27]);

    (new SnapshotValidator($shifted))->validateFile(__DIR__.'/fixtures/synthetic/listings.txt');
})->throws(ColumnMapMismatch::class, 'mls_number is not numeric');

it('throws on an empty snapshot', function () {
    (new SnapshotValidator)->validateString('');
})->throws(ColumnMapMismatch::class, 'empty');

it('tolerates failures below the threshold', function () {
    // One good row, one bad row: 50% failure is not above the 0.5 default.
    $contents = "\"1234567\"\r\n\"not-numeric\"\r\n";

    (new SnapshotValidator)->validateString($contents);

    expect(true)->toBeTrue();
});

it('throws once failures exceed the threshold', function () {
    $contents = "\"1234567\"\r\n\"not-numeric\"\r\n";

    (new SnapshotValidator(failureThreshold: 0.25))->validateString($contents);
})->throws(ColumnMapMismatch::class);

it('only samples the configured number of rows', function () {
    // First row is fine; the bad row sits beyond the sample window.
    $contents = "\"1234567\"\r\n\"not-numeric\"\r\n";

    (new SnapshotValidator(sampleSize: 1, failureThreshold: 0.0))->validateString($contents);

    expect(true)->toBeTrue();
});

it('logs each mismatched row', function () {
    $logger = new class extends AbstractLogger
    {
        /** @var list<string> */
        public array $messages = [];

        public function log($level, Stringable|string $message, array $context = []): void
        {
            $this->messages[] = "{$level}: {$message}";
        }
    };

    $contents = "\"1234567\"\r\n\"not-numeric\"\r\n";

    (new SnapshotValidator(logger: $logger))->validateString($contents);

    expect($logger->messages)->toHaveCount(1)
        ->and($logger->messages[0])->toContain('warning');
});

it('accepts custom checks on top of the defaults', function () {
    $noTestListings = function (array $row, ColumnMap $columns): ?string {
        return $columns->value($row, 'mls_number') === '9999999'
            ? 'mls_number is a test listing'
            : null;
    };

    (new SnapshotValidator(checks: [...SnapshotValidator::defaultChecks(), $noTestListings]))
        ->validateFile(__DIR__.'/fixtures/synthetic/listings.txt');
})->throws(ColumnMapMismatch::class, 'mls_number is a test listing');

it('can replace the default checks entirely', function () {
    // A map that fails every default check passes when no checks run.
    $shifted = ColumnMap::listings()->with(['mls_number' => 27]);

    (new SnapshotValidator($shifted, checks: []))
        ->validateFile(__DIR__.'/fixtures/synthetic/listings.txt');

    expect(true)->toBeTrue();
});

it('checks optional fields only when present', function () {
    $bad = ColumnMap::listings()->with([
        'latitude' => 27,       // street name — not numeric
        'listing_date' => 29,   // postal code — not a date
    ]);

    (new SnapshotValidator($bad))->validateFile(__DIR__.'/fixtures/synthetic/listings.txt');
})->throws(ColumnMapMismatch::class);
