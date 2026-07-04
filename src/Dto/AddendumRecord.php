<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Enums\Language;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the addenda file (ADDENDA.TXT). The feed delivers addendum
 * text chunked into short line parts (~60 chars) — reassemble by
 * (mlsNumber, addendumNumber, language) ordered by partNumber.
 */
final readonly class AddendumRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $addendumNumber,
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
            addendumNumber: Cast::toInt($columns->value($row, 'addendum_number')),
            languageCode: $languageCode,
            language: $languageCode === null ? null : Language::tryFrom($languageCode),
            partNumber: Cast::toInt($columns->value($row, 'part_number')),
            text: $columns->value($row, 'text'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
