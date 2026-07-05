<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Events;

/**
 * A listing present in storage was absent from the full snapshot —
 * sold, expired, or withdrawn. Only the MLS number is available; the
 * feed no longer carries the row.
 */
final readonly class ListingRemoved
{
    public function __construct(
        public string $mlsNumber,
    ) {}
}
