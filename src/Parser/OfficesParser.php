<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\OfficeRecord;

/**
 * Streams the offices file (BUREAUX.TXT) into OfficeRecord objects.
 *
 * @extends FileParser<OfficeRecord>
 */
final class OfficesParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::offices();
    }

    protected function makeRecord(array $row, ColumnMap $columns): OfficeRecord
    {
        return OfficeRecord::fromRow($row, $columns);
    }
}
