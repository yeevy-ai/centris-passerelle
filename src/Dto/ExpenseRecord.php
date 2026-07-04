<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the expenses file (DEPENSES.TXT): a tax or running cost
 * (TAXMUN = municipal tax, TAXSCO = school tax, ENER = energy, …).
 */
final readonly class ExpenseRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?string $typeCode,
        public ?int $amount,
        public ?int $year,
        public ?string $frequencyCode,
        public ?string $categoryCode,
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
            typeCode: $columns->value($row, 'type_code'),
            amount: Cast::toInt($columns->value($row, 'amount')),
            year: Cast::toInt($columns->value($row, 'year')),
            frequencyCode: $columns->value($row, 'frequency_code'),
            categoryCode: $columns->value($row, 'category_code'),
            descriptionFr: $columns->value($row, 'description_fr'),
            descriptionEn: $columns->value($row, 'description_en'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
