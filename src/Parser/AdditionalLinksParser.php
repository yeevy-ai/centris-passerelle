<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\AdditionalLinkRecord;

/**
 * Streams the additional links file (LIENS_ADDITIONNELS.TXT) into
 * AdditionalLinkRecord objects.
 *
 * @extends FileParser<AdditionalLinkRecord>
 */
final class AdditionalLinksParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::additionalLinks();
    }

    protected function makeRecord(array $row, ColumnMap $columns): AdditionalLinkRecord
    {
        return AdditionalLinkRecord::fromRow($row, $columns);
    }
}
