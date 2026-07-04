<?php

declare(strict_types=1);

/*
 * Default column map for the rooms file (PIECES_UNITES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per room, joined to a unit via (mls_number, unit_number).
 * Room type codes: CH = chambre, SDB = salle de bain, SFM = salle
 * familiale, … Level codes: RC = rez-de-chaussée, RJ = rez-de-jardin,
 * SS = sous-sol, numbers for floors.
 */

return [
    'mls_number' => 0,
    'unit_number' => 1,
    'sequence' => 2,
    'type_code' => 3,
    'name_fr' => 4,
    'name_en' => 5,
    'level_code' => 6,
    'dimensions' => 9,       // e.g. "26.8x11.10 P"
    'flooring_code' => 11,   // CERAM, BOIS, TAP, …
    'flooring_fr' => 12,
    'flooring_en' => 13,
    'description_fr' => 15,
    'description_en' => 16,
];
