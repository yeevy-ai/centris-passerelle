<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\AddendumRecord;

/**
 * Streams the addenda file (ADDENDA.TXT) into AddendumRecord objects —
 * text arrives chunked; order chunks by partNumber to reassemble.
 *
 * @extends FileParser<AddendumRecord>
 */
final class AddendaParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::addenda();
    }

    protected function makeRecord(array $row, ColumnMap $columns): AddendumRecord
    {
        return AddendumRecord::fromRow($row, $columns);
    }
}
