<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\RoomRecord;

/**
 * Streams the rooms file (PIECES_UNITES.TXT) into RoomRecord objects.
 *
 * @extends FileParser<RoomRecord>
 */
final class RoomsParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::rooms();
    }

    protected function makeRecord(array $row, ColumnMap $columns): RoomRecord
    {
        return RoomRecord::fromRow($row, $columns);
    }
}
