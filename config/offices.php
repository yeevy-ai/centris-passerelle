<?php

declare(strict_types=1);

/*
 * Default column map for the offices file (BUREAUX.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 */

return [
    'office_code' => 0,
    'firm_code' => 1,
    'name' => 2,
    'civic_number' => 3,
    'street_name' => 4,
    'suite' => 5,
    'city' => 6,
    'province' => 7,
    'postal_code' => 8,
    'phone' => 9,
    'phone_secondary' => 11,
    'fax' => 13,
    'email' => 14,
    'website_url' => 15,
    'firm_number' => 16,
    'photo_url' => 17,
];
