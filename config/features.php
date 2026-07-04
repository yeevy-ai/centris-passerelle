<?php

declare(strict_types=1);

/*
 * Default column map for the features file (CARACTERISTIQUES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per listing feature: category (PART = particularité,
 * ALLE = allée, ARMO = armoires, …) + feature code, with optional
 * quantity and FR/EN values.
 */

return [
    'mls_number' => 0,
    'category_code' => 1,
    'feature_code' => 2,
    'quantity' => 3,
    'value_fr' => 4,
    'value_en' => 5,
    'numeric_value' => 6,
];
