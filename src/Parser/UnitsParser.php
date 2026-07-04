<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\UnitRecord;

/**
 * Streams the detailed units file (UNITES_DETAILLEES.TXT) into
 * UnitRecord objects.
 *
 * @extends FileParser<UnitRecord>
 */
final class UnitsParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::units();
    }

    protected function makeRecord(array $row, ColumnMap $columns): UnitRecord
    {
        return UnitRecord::fromRow($row, $columns);
    }
}
