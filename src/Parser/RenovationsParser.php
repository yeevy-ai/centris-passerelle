<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\RenovationRecord;

/**
 * Streams the renovations file (RENOVATIONS.TXT) into RenovationRecord
 * objects.
 *
 * @extends FileParser<RenovationRecord>
 */
final class RenovationsParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::renovations();
    }

    protected function makeRecord(array $row, ColumnMap $columns): RenovationRecord
    {
        return RenovationRecord::fromRow($row, $columns);
    }
}
