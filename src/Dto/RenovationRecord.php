<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the renovations file (RENOVATIONS.TXT): a declared
 * renovation (CUI = cuisine, TOITR = toiture, CHAUF = chauffage, …).
 */
final readonly class RenovationRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $sequence,
        public ?string $typeCode,
        public ?int $year,
        public ?string $descriptionFr,
        public ?string $descriptionEn,
        public ?int $amount,
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
            typeCode: $columns->value($row, 'type_code'),
            year: Cast::toInt($columns->value($row, 'year')),
            descriptionFr: $columns->value($row, 'description_fr'),
            descriptionEn: $columns->value($row, 'description_en'),
            amount: Cast::toInt($columns->value($row, 'amount')),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
