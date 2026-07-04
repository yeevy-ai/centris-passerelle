<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use DateTimeImmutable;
use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the photos file (PHOTOS.TXT): a single photo reference,
 * ordered by sequence within its listing. The category code identifies
 * the subject (FACA = façade, CUI = cuisine, SDB = salle de bain, …).
 */
final readonly class PhotoRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?int $sequence,
        public ?string $categoryCode,
        public ?string $descriptionFr,
        public ?string $descriptionEn,
        public ?string $url,
        public ?string $photoId,
        public ?DateTimeImmutable $modifiedAt,
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
            categoryCode: $columns->value($row, 'category_code'),
            descriptionFr: $columns->value($row, 'description_fr'),
            descriptionEn: $columns->value($row, 'description_en'),
            url: $columns->value($row, 'url'),
            photoId: $columns->value($row, 'photo_id'),
            modifiedAt: Cast::toDate($columns->value($row, 'modified_at'), '!Y/m/d H:i:s'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
