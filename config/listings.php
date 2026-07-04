<?php

declare(strict_types=1);

/*
 * Default column map for the listings master file (INSCRIPTIONS.TXT),
 * 0-based positions.
 *
 * These positions are COMMUNITY-OBSERVED and may vary by diffusion-agreement
 * version — verify every position against the official Passerelle PDF
 * documentation provided with YOUR agreement, and override this map in your
 * own config rather than editing the package.
 *
 * The file is comma-delimited, quoted, Windows-1252 encoded, no header row,
 * one listing per CRLF-terminated line (~150-160 fields).
 */

return [
    'mls_number' => 0,
    'broker_code' => 2,
    'firm_code' => 3,
    'sale_price' => 6,           // empty for rentals
    'listing_date' => 20,        // YYYY/MM/DD
    'municipality_code' => 22,
    'civic_number' => 25,
    'street_name' => 27,
    'postal_code' => 29,
    'occupancy_delay_fr' => 44,
    'occupancy_delay_en' => 45,
    'genre_code' => 53,          // R = residential
    'type_code' => 54,           // ME/PP/AP/BCB/TV = two-storey/bungalow/condo/commercial/vacant land
    'sale_type_code' => 55,
    'year_built' => 59,
    'lot_frontage' => 62,
    'lot_depth' => 63,
    'lot_dimension_unit' => 65,  // M = metres, P = feet
    'building_frontage' => 71,
    'building_depth' => 72,
    'building_dimension_unit' => 74,
    'living_area' => 75,
    'living_area_unit' => 76,    // MC = m², PC = ft²
    'assessment_year' => 78,
    'assessment_land' => 79,
    'assessment_building' => 80,
    'rooms_total' => 81,
    'bedrooms' => 82,
    'bedrooms_basement' => 83,
    'bathrooms' => 84,
    'powder_rooms' => 85,
    'inclusions_fr' => 100,
    'inclusions_en' => 101,
    'modified_at' => 113,        // YYYY/MM/DD HH:MM:SS
    'status_code' => 115,        // EV = en vigueur (active), VE = vendue (sold)
    'redirect_url' => 132,
    'latitude' => 144,
    'longitude' => 145,
    'description_fr' => 157,     // contains HTML <br/>
    'description_en' => 158,
];
