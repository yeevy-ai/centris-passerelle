<?php

declare(strict_types=1);

/*
 * Default column map for the addenda file (ADDENDA.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * Addendum text is delivered chunked into short line parts (~60 chars):
 * part_number orders the chunks; consumers concatenate them per
 * (mls_number, addendum_number, language_code).
 */

return [
    'mls_number' => 0,
    'addendum_number' => 1,
    'language_code' => 2,   // F = français, A = anglais
    'part_number' => 3,
    'text' => 6,
];
