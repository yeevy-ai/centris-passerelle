<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\FirmRecord;

/**
 * Streams the firms file (FIRMES.TXT) into FirmRecord objects.
 *
 * @extends FileParser<FirmRecord>
 */
final class FirmsParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::firms();
    }

    protected function makeRecord(array $row, ColumnMap $columns): FirmRecord
    {
        return FirmRecord::fromRow($row, $columns);
    }
}
