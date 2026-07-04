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
 *
 * Checks are injectable — add per-agreement invariants or relax the
 * defaults: new SnapshotValidator(checks: [...SnapshotValidator::defaultChecks(), $custom]).
 * Each check receives the raw row and the column map, and returns a
 * failure reason or null.
 */
final class SnapshotValidator
{
    private readonly ColumnMap $columns;

    private readonly LoggerInterface $logger;

    /**
     * @var list<callable(array<int, string|null>, ColumnMap): ?string>
     */
    private readonly array $checks;

    /**
     * @param  list<callable(array<int, string|null>, ColumnMap): ?string>|null  $checks
     */
    public function __construct(
        ?ColumnMap $columns = null,
        private readonly int $sampleSize = 50,
        private readonly float $failureThreshold = 0.5,
        ?LoggerInterface $logger = null,
        ?array $checks = null,
    ) {
        $this->columns = $columns ?? ColumnMap::listings();
        $this->logger = $logger ?? new NullLogger;
        $this->checks = $checks ?? self::defaultChecks();
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
     * @param  array<int, string|null>  $row
     * @return list<string>
     */
    private function check(array $row): array
    {
        $reasons = [];

        foreach ($this->checks as $check) {
            $reason = $check($row, $this->columns);

            if ($reason !== null) {
                $reasons[] = $reason;
            }
        }

        return $reasons;
    }

    /**
     * Invariants that hold for every well-mapped row. Optional fields
     * are only checked when present, so sparse rows pass.
     *
     * @return list<callable(array<int, string|null>, ColumnMap): ?string>
     */
    public static function defaultChecks(): array
    {
        return [
            self::mlsNumberIsNumeric(...),
            self::statusCodeLooksLikeACode(...),
            self::listingDateIsWellFormed(...),
            self::latitudeIsInQuebec(...),
            self::longitudeIsInQuebec(...),
        ];
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private static function mlsNumberIsNumeric(array $row, ColumnMap $columns): ?string
    {
        $mls = $columns->value($row, 'mls_number');

        return $mls === null || ! ctype_digit($mls)
            ? 'mls_number is not numeric'
            : null;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private static function statusCodeLooksLikeACode(array $row, ColumnMap $columns): ?string
    {
        $status = $columns->value($row, 'status_code');

        return $status !== null && preg_match('/^[A-Z]{1,3}$/', $status) !== 1
            ? 'status_code is not an uppercase code'
            : null;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private static function listingDateIsWellFormed(array $row, ColumnMap $columns): ?string
    {
        $listingDate = $columns->value($row, 'listing_date');

        return $listingDate !== null && preg_match('#^\d{4}/\d{2}/\d{2}$#', $listingDate) !== 1
            ? 'listing_date is not YYYY/MM/DD'
            : null;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private static function latitudeIsInQuebec(array $row, ColumnMap $columns): ?string
    {
        $latitude = $columns->value($row, 'latitude');

        return $latitude !== null && (! is_numeric($latitude) || (float) $latitude < 40.0 || (float) $latitude > 65.0)
            ? 'latitude outside the Quebec range'
            : null;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private static function longitudeIsInQuebec(array $row, ColumnMap $columns): ?string
    {
        $longitude = $columns->value($row, 'longitude');

        return $longitude !== null && (! is_numeric($longitude) || (float) $longitude < -85.0 || (float) $longitude > -55.0)
            ? 'longitude outside the Quebec range'
            : null;
    }
}
