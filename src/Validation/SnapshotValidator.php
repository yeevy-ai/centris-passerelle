<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Validation;

use League\Csv\Reader;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Exceptions\ColumnMapMismatch;
use Yeevy\CentrisPasserelle\Support\FeedReader;

/**
 * Samples rows from a snapshot and checks that the column map still
 * lines up with the file. A feed structure change raises no error by
 * itself — it shows up as shifted, garbage data. This guard turns that
 * silent failure into a loud one before anything is imported.
 */
final class SnapshotValidator
{
    private readonly ColumnMap $columns;

    private readonly LoggerInterface $logger;

    public function __construct(
        ?ColumnMap $columns = null,
        private readonly int $sampleSize = 50,
        private readonly float $failureThreshold = 0.5,
        ?LoggerInterface $logger = null,
    ) {
        $this->columns = $columns ?? ColumnMap::listings();
        $this->logger = $logger ?? new NullLogger;
    }

    /**
     * @throws ColumnMapMismatch
     */
    public function validateFile(string $path): void
    {
        $this->validate(FeedReader::fromFile($path));
    }

    /**
     * Validate raw feed bytes as delivered (still Windows-1252 encoded).
     *
     * @throws ColumnMapMismatch
     */
    public function validateString(string $contents): void
    {
        $this->validate(FeedReader::fromString($contents));
    }

    /**
     * @param  Reader<array<int, string|null>>  $reader
     *
     * @throws ColumnMapMismatch
     */
    private function validate(Reader $reader): void
    {
        $sampled = 0;
        $failed = 0;
        $reasons = [];

        foreach ($reader->getRecords() as $line => $row) {
            if ($sampled >= $this->sampleSize) {
                break;
            }

            $sampled++;

            $rowReasons = $this->check($row);

            if ($rowReasons === []) {
                continue;
            }

            $failed++;

            foreach ($rowReasons as $reason) {
                $reasons[$reason] = ($reasons[$reason] ?? 0) + 1;
            }

            $this->logger->warning('Snapshot row does not match the column map.', [
                'line' => $line,
                'reasons' => $rowReasons,
            ]);
        }

        if ($sampled === 0) {
            throw new ColumnMapMismatch(
                'Snapshot is empty — importing it would unpublish every listing.'
            );
        }

        if ($failed / $sampled > $this->failureThreshold) {
            arsort($reasons);

            throw new ColumnMapMismatch(sprintf(
                '%d of %d sampled rows do not match the column map (%s). The feed structure '
                .'may have changed — verify positions against your Passerelle documentation.',
                $failed,
                $sampled,
                implode('; ', array_map(
                    static fn (string $reason, int $count): string => "{$reason} ×{$count}",
                    array_keys($reasons),
                    array_values($reasons),
                )),
            ));
        }
    }

    /**
     * Invariants that hold for every well-mapped row. Optional fields
     * are only checked when present, so sparse rows pass.
     *
     * @param  array<int, string|null>  $row
     * @return list<string>
     */
    private function check(array $row): array
    {
        $reasons = [];

        $mls = $this->columns->value($row, 'mls_number');

        if ($mls === null || ! ctype_digit($mls)) {
            $reasons[] = 'mls_number is not numeric';
        }

        $status = $this->columns->value($row, 'status_code');

        if ($status !== null && preg_match('/^[A-Z]{1,3}$/', $status) !== 1) {
            $reasons[] = 'status_code is not an uppercase code';
        }

        $listingDate = $this->columns->value($row, 'listing_date');

        if ($listingDate !== null && preg_match('#^\d{4}/\d{2}/\d{2}$#', $listingDate) !== 1) {
            $reasons[] = 'listing_date is not YYYY/MM/DD';
        }

        $latitude = $this->columns->value($row, 'latitude');

        if ($latitude !== null && (! is_numeric($latitude) || (float) $latitude < 40.0 || (float) $latitude > 65.0)) {
            $reasons[] = 'latitude outside the Quebec range';
        }

        $longitude = $this->columns->value($row, 'longitude');

        if ($longitude !== null && (! is_numeric($longitude) || (float) $longitude < -85.0 || (float) $longitude > -55.0)) {
            $reasons[] = 'longitude outside the Quebec range';
        }

        return $reasons;
    }
}
