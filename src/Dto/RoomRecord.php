<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the rooms file (PIECES_UNITES.TXT): a room within a unit,
 * joined via (mlsNumber, unitNumber). Room type codes: CH = chambre,
 * SDB = salle de bain, … Level codes: RC = rez-de-chaussée, SS =
 * sous-sol, numbers for floors.
 */
final readonly class RoomRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $unitNumber,
        public ?int $sequence,
        public ?string $typeCode,
        public ?string $nameFr,
        public ?string $nameEn,
        public ?string $levelCode,
        public ?string $dimensions,
        public ?string $flooringCode,
        public ?string $flooringFr,
        public ?string $flooringEn,
        public ?string $descriptionFr,
        public ?string $descriptionEn,
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
            sequence: Cast::toInt($columns->value($row, 'sequence')),
            typeCode: $columns->value($row, 'type_code'),
            nameFr: $columns->value($row, 'name_fr'),
            nameEn: $columns->value($row, 'name_en'),
            levelCode: $columns->value($row, 'level_code'),
            dimensions: $columns->value($row, 'dimensions'),
            flooringCode: $columns->value($row, 'flooring_code'),
            flooringFr: $columns->value($row, 'flooring_fr'),
            flooringEn: $columns->value($row, 'flooring_en'),
            descriptionFr: $columns->value($row, 'description_fr'),
            descriptionEn: $columns->value($row, 'description_en'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
