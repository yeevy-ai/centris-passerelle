<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use DateTimeImmutable;
use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the open houses file (VISITES_LIBRES.TXT): a scheduled
 * open-house window for a listing.
 */
final readonly class OpenHouseRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $sequence,
        public ?DateTimeImmutable $startDate,
        public ?DateTimeImmutable $endDate,
        public ?string $startTime,
        public ?string $endTime,
        public ?string $typeCode,
        public string $dirtyHash,
        public array $row,
    ) {}

    /**
     * @param  array<int, string|null>  $row
     */
    public static function fromRow(array $row, ColumnMap $columns): self
    {
        $mlsNumber = $columns->value($row, 'mls_number');

        if ($mlsNumber === null) {
            throw new InvalidArgumentException('Row has no MLS number — the join key is mandatory.');
        }

        return new self(
            mlsNumber: $mlsNumber,
            sequence: Cast::toInt($columns->value($row, 'sequence')),
            startDate: Cast::toDate($columns->value($row, 'start_date'), '!Y/m/d'),
            endDate: Cast::toDate($columns->value($row, 'end_date'), '!Y/m/d'),
            startTime: $columns->value($row, 'start_time'),
            endTime: $columns->value($row, 'end_time'),
            typeCode: $columns->value($row, 'type_code'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
