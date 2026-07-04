<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use DateTimeImmutable;
use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Enums\Language;
use Yeevy\CentrisPasserelle\Support\Cast;
use Yeevy\CentrisPasserelle\Support\RowHash;

/**
 * One row of the brokers file (MEMBRES.TXT): a board member
 * (broker/agent), referenced from listings by brokerCode.
 */
final readonly class BrokerRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $brokerCode,
        public ?string $officeCode,
        public ?string $licenseNumber,
        public ?string $titleCode,
        public ?string $lastName,
        public ?string $firstName,
        public ?string $phone,
        public ?string $phoneSecondary,
        public ?string $phoneTertiary,
        public ?string $email,
        public ?string $websiteUrl,
        public ?string $languageCode,
        public ?Language $language,
        public ?string $photoUrl,
        public ?DateTimeImmutable $modifiedAt,
        public ?string $companyName,
        public ?string $companyNoteFr,
        public ?string $companyNoteEn,
        public ?string $videoUrlFr,
        public ?string $videoUrlEn,
        public ?string $bioFr,
        public ?string $bioEn,
        public string $dirtyHash,
        public array $row,
    ) {}

    /**
     * @param  array<int, string|null>  $row
     */
    public static function fromRow(array $row, ColumnMap $columns): self
    {
        $brokerCode = $columns->value($row, 'broker_code');

        if ($brokerCode === null) {
            throw new InvalidArgumentException('Row has no broker code — the reference key is mandatory.');
        }

        $languageCode = $columns->value($row, 'language_code');

        return new self(
            brokerCode: $brokerCode,
            officeCode: $columns->value($row, 'office_code'),
            licenseNumber: $columns->value($row, 'license_number'),
            titleCode: $columns->value($row, 'title_code'),
            lastName: $columns->value($row, 'last_name'),
            firstName: $columns->value($row, 'first_name'),
            phone: $columns->value($row, 'phone'),
            phoneSecondary: $columns->value($row, 'phone_secondary'),
            phoneTertiary: $columns->value($row, 'phone_tertiary'),
            email: $columns->value($row, 'email'),
            websiteUrl: $columns->value($row, 'website_url'),
            languageCode: $languageCode,
            language: $languageCode === null ? null : Language::tryFrom($languageCode),
            photoUrl: $columns->value($row, 'photo_url'),
            modifiedAt: Cast::toDate($columns->value($row, 'modified_at'), '!Y/m/d H:i:s'),
            companyName: $columns->value($row, 'company_name'),
            companyNoteFr: $columns->value($row, 'company_note_fr'),
            companyNoteEn: $columns->value($row, 'company_note_en'),
            videoUrlFr: $columns->value($row, 'video_url_fr'),
            videoUrlEn: $columns->value($row, 'video_url_en'),
            bioFr: $columns->value($row, 'bio_fr'),
            bioEn: $columns->value($row, 'bio_en'),
            dirtyHash: RowHash::of($row),
            row: $row,
        );
    }
}
