<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the additional links file (LIENS_ADDITIONNELS.TXT): an
 * external link (VISVID = video, VIS3D = virtual tour, …) with
 * per-language URLs.
 */
final readonly class AdditionalLinkRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $sequence,
        public ?string $typeCode,
        public ?string $urlFr,
        public ?string $urlEn,
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
            urlFr: $columns->value($row, 'url_fr'),
            urlEn: $columns->value($row, 'url_en'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
