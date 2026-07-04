<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\ExpenseRecord;

/**
 * Streams the expenses file (DEPENSES.TXT) into ExpenseRecord objects.
 *
 * @extends FileParser<ExpenseRecord>
 */
final class ExpensesParser extends FileParser
{
    protected function defaultColumns(): ColumnMap
    {
        return ColumnMap::expenses();
    }

    protected function makeRecord(array $row, ColumnMap $columns): ExpenseRecord
    {
        return ExpenseRecord::fromRow($row, $columns);
    }
}
