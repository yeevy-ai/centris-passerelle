<?php

declare(strict_types=1);

/*
 * Default column map for the remarks file (REMARQUES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per listing per language (language_code F/A). Long texts can
 * be split across rows — part_number orders the chunks for reassembly.
 */

return [
    'mls_number' => 0,
    'remark_number' => 1,
    'language_code' => 2,   // F = français, A = anglais
    'part_number' => 3,
    'text' => 6,
];
