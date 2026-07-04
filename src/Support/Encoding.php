<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Support;

/**
 * Centris Passerelle files are delivered in Windows-1252. Every byte of feed
 * content must pass through here exactly once, before any CSV parsing.
 */
final class Encoding
{
    public static function toUtf8(string $content): string
    {
        return mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
    }
}
