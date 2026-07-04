<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the offices file (BUREAUX.TXT): a physical agency office,
 * referenced from brokers by officeCode and owned by a firm.
 */
final readonly class OfficeRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $officeCode,
        public ?string $firmCode,
        public ?string $name,
        public ?string $civicNumber,
        public ?string $streetName,
        public ?string $suite,
        public ?string $city,
        public ?string $province,
        public ?string $postalCode,
        public ?string $phone,
        public ?string $phoneSecondary,
        public ?string $fax,
        public ?string $email,
        public ?string $websiteUrl,
        public ?int $firmNumber,
        public ?string $photoUrl,
        public string $dirtyHash,
        public array $row,
    ) {}

    /**
     * @param  array<int, string|null>  $row
     */
    public static function fromRow(array $row, ColumnMap $columns): self
    {
        $officeCode = $columns->value($row, 'office_code');

        if ($officeCode === null) {
            throw new InvalidArgumentException('Row has no office code — the reference key is mandatory.');
        }

        return new self(
            officeCode: $officeCode,
            firmCode: $columns->value($row, 'firm_code'),
            name: $columns->value($row, 'name'),
            civicNumber: $columns->value($row, 'civic_number'),
            streetName: $columns->value($row, 'street_name'),
            suite: $columns->value($row, 'suite'),
            city: $columns->value($row, 'city'),
            province: $columns->value($row, 'province'),
            postalCode: $columns->value($row, 'postal_code'),
            phone: $columns->value($row, 'phone'),
            phoneSecondary: $columns->value($row, 'phone_secondary'),
            fax: $columns->value($row, 'fax'),
            email: $columns->value($row, 'email'),
            websiteUrl: $columns->value($row, 'website_url'),
            firmNumber: Cast::toInt($columns->value($row, 'firm_number')),
            photoUrl: $columns->value($row, 'photo_url'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
