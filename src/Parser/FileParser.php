<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Parser;

use Generator;
use InvalidArgumentException;
use League\Csv\Reader;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yeevy\CentrisPasserelle\Config\ColumnMap;
use Yeevy\CentrisPasserelle\Support\FeedReader;

/**
 * Base for every feed-file parser: streams a Windows-1252 file lazily,
 * yields one typed record per row, and logs and skips rows that cannot
 * become a record instead of aborting the whole snapshot.
 *
 * @template TRecord of object
 */
abstract class FileParser
{
    private readonly ColumnMap $columns;

    private readonly LoggerInterface $logger;

    public function __construct(?ColumnMap $columns = null, ?LoggerInterface $logger = null)
    {
        $this->columns = $columns ?? $this->defaultColumns();
        $this->logger = $logger ?? new NullLogger;
    }

    abstract protected function defaultColumns(): ColumnMap;

    /**
     * @param  array<int, string|null>  $row
     * @return TRecord
     *
     * @throws InvalidArgumentException when the row cannot become a record
     */
    abstract protected function makeRecord(array $row, ColumnMap $columns): object;

    /**
     * @return Generator<int, TRecord>
     */
    public function parseFile(string $path): Generator
    {
        return $this->records(FeedReader::fromFile($path));
    }

    /**
     * Parse raw feed bytes as delivered (still Windows-1252 encoded).
     *
     * @return Generator<int, TRecord>
     */
    public function parseString(string $contents): Generator
    {
        return $this->records(FeedReader::fromString($contents));
    }

    /**
     * @param  Reader<array<int, string|null>>  $reader
     * @return Generator<int, TRecord>
     */
    private function records(Reader $reader): Generator
    {
        foreach ($reader->getRecords() as $line => $row) {
            try {
                yield $this->makeRecord($row, $this->columns);
            } catch (InvalidArgumentException $exception) {
                $this->logger->warning('Skipping unparsable feed row.', [
                    'parser' => static::class,
                    'line' => $line,
                    'reason' => $exception->getMessage(),
                ]);
            }
        }
    }
}
