<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Config;

use InvalidArgumentException;

/**
 * Field-name => 0-based column position map for a feed file.
 *
 * The shipped defaults are community-observed and may vary by
 * diffusion-agreement version — consumers should override positions
 * verified against their own Passerelle documentation.
 */
final class ColumnMap
{
    /**
     * @param  array<string, int>  $map
     */
    public function __construct(private readonly array $map) {}

    /**
     * Shipped listings map. Pass a profile name to load an alternative
     * layout (config/listings-{profile}.php) when Centris introduces a
     * new agreement version — old profiles keep working forever instead
     * of being overwritten.
     */
    public static function listings(?string $profile = null): self
    {
        if ($profile !== null && preg_match('/^[A-Za-z0-9_-]+$/', $profile) !== 1) {
            throw new InvalidArgumentException("Invalid column map profile name: {$profile}");
        }

        $file = $profile === null ? 'listings.php' : "listings-{$profile}.php";

        return self::fromFile(dirname(__DIR__, 2).'/config/'.$file);
    }

    public static function fromFile(string $path): self
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException("Column map file not found: {$path}");
        }

        $map = require $path;

        if (! is_array($map)) {
            throw new InvalidArgumentException("Column map file must return an array: {$path}");
        }

        $validated = [];

        foreach ($map as $field => $position) {
            if (! is_string($field) || ! is_int($position) || $position < 0) {
                throw new InvalidArgumentException(
                    "Column map entries must be string => non-negative int: {$path}"
                );
            }

            $validated[$field] = $position;
        }

        return new self($validated);
    }

    /**
     * @param  array<string, int>  $overrides
     */
    public function with(array $overrides): self
    {
        return new self(array_merge($this->map, $overrides));
    }

    public function position(string $field): ?int
    {
        return $this->map[$field] ?? null;
    }

    /**
     * Value of a mapped field within a parsed row.
     * Missing positions and empty fields both come back as null.
     *
     * @param  array<int, string|null>  $row
     */
    public function value(array $row, string $field): ?string
    {
        $position = $this->map[$field] ?? null;

        if ($position === null) {
            return null;
        }

        $value = $row[$position] ?? null;

        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
