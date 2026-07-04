<?php

declare(strict_types=1);

/*
 * Default column map for the photos file (PHOTOS.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per photo, ordered by sequence. category_code identifies the
 * subject (FACA = façade, CUI = cuisine, SDB = salle de bain, …).
 */

return [
    'mls_number' => 0,
    'sequence' => 1,
    'category_code' => 3,
    'description_fr' => 4,
    'description_en' => 5,
    'url' => 6,
    'photo_id' => 7,
    'modified_at' => 8,     // YYYY/MM/DD HH:MM:SS
];
