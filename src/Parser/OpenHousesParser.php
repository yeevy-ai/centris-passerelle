<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\OpenHouseRecord;

/**
 * Streams the open houses file (VISITES_LIBRES.TXT) into
 * OpenHouseRecord objects.
 *
 * @extends FileParser<OpenHouseRecord>
 */
final class OpenHousesParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::openHouses();
    }

    protected function makeRecord(array $row, ColumnMap $columns): OpenHouseRecord
    {
        return OpenHouseRecord::fromRow($row, $columns);
    }
}
