<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Support;

/**
 * Stable hash of a raw feed row, used to skip unchanged records on upsert.
 */
final class RowHash
{
    /**
     * @param  array<int, string|null>  $row
     */
    public static function of(array $row): string
    {
        return hash('sha256', implode("\x1F", array_map(
            static fn (?string $value): string => $value ?? '',
            $row,
        )));
    }
}
