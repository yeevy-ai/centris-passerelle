<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Support;

use DateTimeImmutable;

/**
 * Lenient scalar casts for feed values. The feed carries hand-entered
 * data — malformed values become null rather than exceptions.
 */
final class Cast
{
    public static function toInt(?string $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    public static function toFloat(?string $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    public static function toDate(?string $value, string $format): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat($format, $value);

        return $date === false ? null : $date;
    }
}
