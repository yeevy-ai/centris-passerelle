<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Events;

use Yeevy\CentrisPasserelle\Dto\ListingRecord;

/**
 * A listing appeared in the snapshot that storage did not know about.
 */
final readonly class ListingCreated
{
    public function __construct(
        public ListingRecord $listing,
    ) {}
}
