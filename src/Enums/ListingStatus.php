<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Enums;

/**
 * Listing status codes as delivered in the feed.
 * EV = "en vigueur" (active), VE = "vendue" (sold).
 * Listings absent from a snapshot are removed — that state never
 * appears in the file; it is detected during reconciliation.
 */
enum ListingStatus: string
{
    case Active = 'EV';
    case Sold = 'VE';
}
