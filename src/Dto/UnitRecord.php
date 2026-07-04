<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the detailed units file (UNITES_DETAILLEES.TXT): a unit
 * of a property (PRINCIPAL = main unit, LOGEMENT = rental unit,
 * INTG = intergenerational). Sparse amenity flags are unmapped —
 * reach them via the raw row.
 */
final readonly class UnitRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $unitNumber,
        public ?string $typeCode,
        public ?int $roomsTotal,
        public ?int $bedrooms,
        public ?string $noteFr,
        public ?string $noteEn,
        public ?float $livingArea,
        public ?string $livingAreaUnit,
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
            unitNumber: Cast::toInt($columns->value($row, 'unit_number')),
            typeCode: $columns->value($row, 'type_code'),
            roomsTotal: Cast::toInt($columns->value($row, 'rooms_total')),
            bedrooms: Cast::toInt($columns->value($row, 'bedrooms')),
            noteFr: $columns->value($row, 'note_fr'),
            noteEn: $columns->value($row, 'note_en'),
            livingArea: Cast::toFloat($columns->value($row, 'living_area')),
            livingAreaUnit: $columns->value($row, 'living_area_unit'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
