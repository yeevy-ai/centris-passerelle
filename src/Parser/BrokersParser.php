<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\BrokerRecord;

/**
 * Streams the brokers file (MEMBRES.TXT) into BrokerRecord objects.
 *
 * @extends FileParser<BrokerRecord>
 */
final class BrokersParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::brokers();
    }

    protected function makeRecord(array $row, ColumnMap $columns): BrokerRecord
    {
        return BrokerRecord::fromRow($row, $columns);
    }
}
