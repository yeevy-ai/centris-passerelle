<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Generator;
use InvalidArgumentException;
use League\Csv\CharsetConverter;
use League\Csv\Reader;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Dto\ListingRecord;

/**
 * Streams the listings master file (INSCRIPTIONS.TXT) into ListingRecord
 * objects: comma-delimited, quoted, no header row, Windows-1252, CRLF.
 *
 * Rows that cannot be turned into a record (no MLS number) are logged
 * and skipped rather than aborting the whole snapshot.
 */
final class ListingsParser
{
    private readonly ColumnMap $columns;

    private readonly LoggerInterface $logger;

    public function __construct(?ColumnMap $columns = null, ?LoggerInterface $logger = null)
    {
        $this->columns = $columns ?? ColumnMap::listings();
        $this->logger = $logger ?? new NullLogger;
    }

    /**
     * @return Generator<int, ListingRecord>
     */
    public function parseFile(string $path): Generator
    {
        return $this->records(Reader::from($path, 'r'));
    }

    /**
     * Parse raw feed bytes as delivered (still Windows-1252 encoded).
     *
     * @return Generator<int, ListingRecord>
     */
    public function parseString(string $contents): Generator
    {
        return $this->records(Reader::fromString($contents));
    }

    /**
     * @param  Reader<array<int, string|null>>  $reader
     * @return Generator<int, ListingRecord>
     */
    private function records(Reader $reader): Generator
    {
        CharsetConverter::addTo($reader, 'windows-1252', 'utf-8');

        foreach ($reader->getRecords() as $line => $row) {
            try {
                yield ListingRecord::fromRow($row, $this->columns);
            } catch (InvalidArgumentException $exception) {
                $this->logger->warning('Skipping unparsable listings row.', [
                    'line' => $line,
                    'reason' => $exception->getMessage(),
                ]);
            }
        }
    }
}
