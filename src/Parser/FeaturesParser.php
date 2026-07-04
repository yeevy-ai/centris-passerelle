<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\FeatureRecord;

/**
 * Streams the features file (CARACTERISTIQUES.TXT) into FeatureRecord
 * objects.
 *
 * @extends FileParser<FeatureRecord>
 */
final class FeaturesParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::features();
    }

    protected function makeRecord(array $row, ColumnMap $columns): FeatureRecord
    {
        return FeatureRecord::fromRow($row, $columns);
    }
}
