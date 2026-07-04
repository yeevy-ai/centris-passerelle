<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\PhotoRecord;

/**
 * Streams the photos file (PHOTOS.TXT) into PhotoRecord objects — one
 * row per photo, ordered by sequence within each listing.
 *
 * @extends FileParser<PhotoRecord>
 */
final class PhotosParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::photos();
    }

    protected function makeRecord(array $row, ColumnMap $columns): PhotoRecord
    {
        return PhotoRecord::fromRow($row, $columns);
    }
}
