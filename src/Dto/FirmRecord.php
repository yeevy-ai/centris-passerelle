<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the firms file (FIRMES.TXT): a real-estate agency,
 * referenced from listings and offices by firmCode.
 */
final readonly class FirmRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $firmCode,
        public ?string $name,
        public ?string $licenseNumber,
        public ?string $titleCode,
        public ?string $bannerCode,
        public ?string $groupCode,
        public ?int $firmNumber,
        public string $dirtyHash,
        public array $row,
    ) {}

    /**
     * @param  array<int, string|null>  $row
     */
    public static function fromRow(array $row, ColumnMap $columns): self
    {
        $firmCode = $columns->value($row, 'firm_code');

        if ($firmCode === null) {
            throw new InvalidArgumentException('Row has no firm code — the reference key is mandatory.');
        }

        return new self(
            firmCode: $firmCode,
            name: $columns->value($row, 'name'),
            licenseNumber: $columns->value($row, 'license_number'),
            titleCode: $columns->value($row, 'title_code'),
            bannerCode: $columns->value($row, 'banner_code'),
            groupCode: $columns->value($row, 'group_code'),
            firmNumber: Cast::toInt($columns->value($row, 'firm_number')),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
