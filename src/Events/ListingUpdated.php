<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Events;

use Yeevy\CentrisPasserelle\Dto\ListingRecord;

/**
 * A known listing's row changed since the last snapshot (dirty hash
 * mismatch) — including EV → VE status transitions (sold).
 */
final readonly class ListingUpdated
{
    public function __construct(
        public ListingRecord $listing,
    ) {}
}
