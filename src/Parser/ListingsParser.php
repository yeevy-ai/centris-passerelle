<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\ListingRecord;

/**
 * Streams the listings master file (INSCRIPTIONS.TXT) into
 * ListingRecord objects: comma-delimited, quoted, no header row,
 * Windows-1252, CRLF, ~160 positional columns.
 *
 * @extends FileParser<ListingRecord>
 */
final class ListingsParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::listings();
    }

    protected function makeRecord(array $row, ColumnMap $columns): ListingRecord
    {
        return ListingRecord::fromRow($row, $columns);
    }
}
