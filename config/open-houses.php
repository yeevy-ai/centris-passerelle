<?php

declare(strict_types=1);

/*
 * Default column map for the open houses file (VISITES_LIBRES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 */

return [
    'mls_number' => 0,
    'sequence' => 1,
    'start_date' => 2,       // YYYY/MM/DD
    'end_date' => 3,
    'start_time' => 4,       // HH:MM
    'end_time' => 5,
    'type_code' => 8,        // VL = visite libre
];
