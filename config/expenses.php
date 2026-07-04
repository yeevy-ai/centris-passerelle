<?php

declare(strict_types=1);

/*
 * Default column map for the expenses file (DEPENSES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per listing expense: taxes and running costs
 * (TAXMUN = municipal tax, TAXSCO = school tax, ENER = energy, …).
 */

return [
    'mls_number' => 0,
    'type_code' => 1,
    'amount' => 2,
    'year' => 3,
    'frequency_code' => 5,   // A = annual
    'category_code' => 6,    // e.g. DEPGEN
    'description_fr' => 7,
    'description_en' => 8,
];
