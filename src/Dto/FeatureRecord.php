<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the features file (CARACTERISTIQUES.TXT): a coded listing
 * feature within a category, with optional quantity and FR/EN values.
 */
final readonly class FeatureRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?string $categoryCode,
        public ?string $featureCode,
        public ?int $quantity,
        public ?string $valueFr,
        public ?string $valueEn,
        public ?float $numericValue,
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
            categoryCode: $columns->value($row, 'category_code'),
            featureCode: $columns->value($row, 'feature_code'),
            quantity: Cast::toInt($columns->value($row, 'quantity')),
            valueFr: $columns->value($row, 'value_fr'),
            valueEn: $columns->value($row, 'value_en'),
            numericValue: Cast::toFloat($columns->value($row, 'numeric_value')),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
