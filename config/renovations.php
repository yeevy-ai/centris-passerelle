<?php

declare(strict_types=1);

/*
 * Default column map for the renovations file (RENOVATIONS.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per declared renovation (CUI = cuisine, TOITR = toiture,
 * CHAUF = chauffage, …).
 */

return [
    'mls_number' => 0,
    'sequence' => 1,
    'type_code' => 2,
    'year' => 3,
    'description_fr' => 5,
    'description_en' => 6,
    'amount' => 7,
];
