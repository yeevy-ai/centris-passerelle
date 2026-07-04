<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\RemarkRecord;

/**
 * Streams the remarks file (REMARQUES.TXT) into RemarkRecord objects —
 * one row per listing per language (F/A).
 *
 * @extends FileParser<RemarkRecord>
 */
final class RemarksParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::remarks();
    }

    protected function makeRecord(array $row, ColumnMap $columns): RemarkRecord
    {
        return RemarkRecord::fromRow($row, $columns);
    }
}
