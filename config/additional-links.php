<?php

declare(strict_types=1);

/*
 * Default column map for the additional links file
 * (LIENS_ADDITIONNELS.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per external link (VISVID = video, VIS3D = virtual tour, …),
 * with per-language URLs.
 */

return [
    'mls_number' => 0,
    'sequence' => 1,
    'type_code' => 2,
    'url_fr' => 3,
    'url_en' => 4,
];
