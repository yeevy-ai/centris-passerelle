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
     * Shipped listings map (INSCRIPTIONS.TXT). Pass a profile name to
     * load an alternative layout (config/listings-{profile}.php) when
     * Centris introduces a new agreement version — old profiles keep
     * working forever instead of being overwritten.
     */
    public static function listings(?string $profile = null): self
    {
        return self::shipped('listings', $profile);
    }

    /**
     * Shipped remarks map (REMARQUES.TXT).
     */
    public static function remarks(?string $profile = null): self
    {
        return self::shipped('remarks', $profile);
    }

    /**
     * Shipped addenda map (ADDENDA.TXT).
     */
    public static function addenda(?string $profile = null): self
    {
        return self::shipped('addenda', $profile);
    }

    /**
     * Shipped photos map (PHOTOS.TXT).
     */
    public static function photos(?string $profile = null): self
    {
        return self::shipped('photos', $profile);
    }

    /**
     * Shipped brokers map (MEMBRES.TXT).
     */
    public static function brokers(?string $profile = null): self
    {
        return self::shipped('brokers', $profile);
    }

    /**
     * Shipped firms map (FIRMES.TXT).
     */
    public static function firms(?string $profile = null): self
    {
        return self::shipped('firms', $profile);
    }

    /**
     * Shipped offices map (BUREAUX.TXT).
     */
    public static function offices(?string $profile = null): self
    {
        return self::shipped('offices', $profile);
    }

    /**
     * Shipped features map (CARACTERISTIQUES.TXT).
     */
    public static function features(?string $profile = null): self
    {
        return self::shipped('features', $profile);
    }

    /**
     * Shipped expenses map (DEPENSES.TXT).
     */
    public static function expenses(?string $profile = null): self
    {
        return self::shipped('expenses', $profile);
    }

    /**
     * Shipped renovations map (RENOVATIONS.TXT).
     */
    public static function renovations(?string $profile = null): self
    {
        return self::shipped('renovations', $profile);
    }

    /**
     * Shipped additional links map (LIENS_ADDITIONNELS.TXT).
     */
    public static function additionalLinks(?string $profile = null): self
    {
        return self::shipped('additional-links', $profile);
    }

    /**
     * Shipped open houses map (VISITES_LIBRES.TXT).
     */
    public static function openHouses(?string $profile = null): self
    {
        return self::shipped('open-houses', $profile);
    }

    /**
     * Shipped detailed units map (UNITES_DETAILLEES.TXT).
     */
    public static function units(?string $profile = null): self
    {
        return self::shipped('units', $profile);
    }

    /**
     * Shipped rooms map (PIECES_UNITES.TXT).
     */
    public static function rooms(?string $profile = null): self
    {
        return self::shipped('rooms', $profile);
    }

    private static function shipped(string $name, ?string $profile): self
    {
        if ($profile !== null && preg_match('/^[A-Za-z0-9_-]+$/', $profile) !== 1) {
            throw new InvalidArgumentException("Invalid column map profile name: {$profile}");
        }

        $file = $profile === null ? "{$name}.php" : "{$name}-{$profile}.php";

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
