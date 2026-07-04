<?php

declare(strict_types=1);

/*
 * Default column map for the detailed units file
 * (UNITES_DETAILLEES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per unit of a property (PRINCIPAL = main unit,
 * LOGEMENT = rental unit, INTG = intergenerational). Columns 5-12 are
 * sparse O/N amenity flags left unmapped — reach them via the raw row.
 */

return [
    'mls_number' => 0,
    'unit_number' => 1,
    'type_code' => 2,
    'rooms_total' => 3,
    'bedrooms' => 4,
    'note_fr' => 15,
    'note_en' => 16,
    'living_area' => 18,
    'living_area_unit' => 19,   // PC = ft², MC = m²
];
