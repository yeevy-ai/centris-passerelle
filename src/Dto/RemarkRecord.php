<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Enums\Language;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the remarks file (REMARQUES.TXT): a listing's public
 * description in one language. Long texts can span several rows —
 * reassemble by (mlsNumber, remarkNumber, language) ordered by
 * partNumber.
 */
final readonly class RemarkRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $remarkNumber,
        public ?string $languageCode,
        public ?Language $language,
        public ?int $partNumber,
        public ?string $text,
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

        $languageCode = $columns->value($row, 'language_code');

        return new self(
            mlsNumber: $mlsNumber,
            remarkNumber: Cast::toInt($columns->value($row, 'remark_number')),
            languageCode: $languageCode,
            language: $languageCode === null ? null : Language::tryFrom($languageCode),
            partNumber: Cast::toInt($columns->value($row, 'part_number')),
            text: $columns->value($row, 'text'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
