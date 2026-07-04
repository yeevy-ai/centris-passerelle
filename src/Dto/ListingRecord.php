<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Dto;

use DateTimeImmutable;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Enums\ListingStatus;

/**
 * One row of the listings master file (INSCRIPTIONS.TXT), typed.
 *
 * The raw row is kept alongside the mapped fields so consumers can
 * reach columns the shipped map does not cover.
 */
final readonly class ListingRecord
{
    /**
     * @param  array<int, string|null>  $row
     */
    public function __construct(
        public string $mlsNumber,
        public ?string $brokerCode,
        public ?string $firmCode,
        public ?int $salePrice,
        public ?DateTimeImmutable $listingDate,
        public ?string $municipalityCode,
        public ?string $civicNumber,
        public ?string $streetName,
        public ?string $postalCode,
        public ?string $occupancyDelayFr,
        public ?string $occupancyDelayEn,
        public ?string $genreCode,
        public ?string $typeCode,
        public ?string $saleTypeCode,
        public ?int $yearBuilt,
        public ?float $lotFrontage,
        public ?float $lotDepth,
        public ?string $lotDimensionUnit,
        public ?float $buildingFrontage,
        public ?float $buildingDepth,
        public ?string $buildingDimensionUnit,
        public ?float $livingArea,
        public ?string $livingAreaUnit,
        public ?int $assessmentYear,
        public ?int $assessmentLand,
        public ?int $assessmentBuilding,
        public ?int $roomsTotal,
        public ?int $bedrooms,
        public ?int $bedroomsBasement,
        public ?int $bathrooms,
        public ?int $powderRooms,
        public ?string $inclusionsFr,
        public ?string $inclusionsEn,
        public ?DateTimeImmutable $modifiedAt,
        public ?string $statusCode,
        public ?ListingStatus $status,
        public ?string $redirectUrl,
        public ?float $latitude,
        public ?float $longitude,
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
            throw new \InvalidArgumentException('Row has no MLS number — the upsert key is mandatory.');
        }

        $statusCode = $columns->value($row, 'status_code');

        return new self(
            mlsNumber: $mlsNumber,
            brokerCode: $columns->value($row, 'broker_code'),
            firmCode: $columns->value($row, 'firm_code'),
            salePrice: self::toInt($columns->value($row, 'sale_price')),
            listingDate: self::toDate($columns->value($row, 'listing_date'), '!Y/m/d'),
            municipalityCode: $columns->value($row, 'municipality_code'),
            civicNumber: $columns->value($row, 'civic_number'),
            streetName: $columns->value($row, 'street_name'),
            postalCode: $columns->value($row, 'postal_code'),
            occupancyDelayFr: $columns->value($row, 'occupancy_delay_fr'),
            occupancyDelayEn: $columns->value($row, 'occupancy_delay_en'),
            genreCode: $columns->value($row, 'genre_code'),
            typeCode: $columns->value($row, 'type_code'),
            saleTypeCode: $columns->value($row, 'sale_type_code'),
            yearBuilt: self::toInt($columns->value($row, 'year_built')),
            lotFrontage: self::toFloat($columns->value($row, 'lot_frontage')),
            lotDepth: self::toFloat($columns->value($row, 'lot_depth')),
            lotDimensionUnit: $columns->value($row, 'lot_dimension_unit'),
            buildingFrontage: self::toFloat($columns->value($row, 'building_frontage')),
            buildingDepth: self::toFloat($columns->value($row, 'building_depth')),
            buildingDimensionUnit: $columns->value($row, 'building_dimension_unit'),
            livingArea: self::toFloat($columns->value($row, 'living_area')),
            livingAreaUnit: $columns->value($row, 'living_area_unit'),
            assessmentYear: self::toInt($columns->value($row, 'assessment_year')),
            assessmentLand: self::toInt($columns->value($row, 'assessment_land')),
            assessmentBuilding: self::toInt($columns->value($row, 'assessment_building')),
            roomsTotal: self::toInt($columns->value($row, 'rooms_total')),
            bedrooms: self::toInt($columns->value($row, 'bedrooms')),
            bedroomsBasement: self::toInt($columns->value($row, 'bedrooms_basement')),
            bathrooms: self::toInt($columns->value($row, 'bathrooms')),
            powderRooms: self::toInt($columns->value($row, 'powder_rooms')),
            inclusionsFr: $columns->value($row, 'inclusions_fr'),
            inclusionsEn: $columns->value($row, 'inclusions_en'),
            modifiedAt: self::toDate($columns->value($row, 'modified_at'), '!Y/m/d H:i:s'),
            statusCode: $statusCode,
            status: $statusCode === null ? null : ListingStatus::tryFrom($statusCode),
            redirectUrl: $columns->value($row, 'redirect_url'),
            latitude: self::toFloat($columns->value($row, 'latitude')),
            longitude: self::toFloat($columns->value($row, 'longitude')),
            descriptionFr: $columns->value($row, 'description_fr'),
            descriptionEn: $columns->value($row, 'description_en'),
            dirtyHash: self::hashRow($row),
            row: $row,
        );
    }

    /**
     * Stable hash of the raw row, used to skip unchanged records on upsert.
     *
     * @param  array<int, string|null>  $row
     */
    public static function hashRow(array $row): string
    {
        return hash('sha256', implode("\x1F", array_map(
            static fn (?string $value): string => $value ?? '',
            $row,
        )));
    }

    private static function toInt(?string $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private static function toFloat(?string $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private static function toDate(?string $value, string $format): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat($format, $value);

        return $date === false ? null : $date;
    }
}
