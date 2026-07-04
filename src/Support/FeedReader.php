<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Support;

use League\Csv\CharsetConverter;
use League\Csv\Reader;

/**
 * Opens feed files or raw feed bytes as UTF-8 CSV readers — the single
 * place where the feed's delivery format (Windows-1252, comma-delimited,
 * quoted, no header row) is wired up.
 */
final class FeedReader
{
    /**
     * @return Reader<array<int, string|null>>
     */
    public static function fromFile(string $path): Reader
    {
        return self::toUtf8Reader(Reader::from($path, 'r'));
    }

    /**
     * @return Reader<array<int, string|null>>
     */
    public static function fromString(string $contents): Reader
    {
        return self::toUtf8Reader(Reader::fromString($contents));
    }

    /**
     * @param  Reader<array<int, string|null>>  $reader
     * @return Reader<array<int, string|null>>
     */
    private static function toUtf8Reader(Reader $reader): Reader
    {
        CharsetConverter::addTo($reader, Encoding::FEED, 'UTF-8');

        return $reader;
    }
}
