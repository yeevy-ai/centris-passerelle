<?php

declare(strict_types=1);

/*
 * Default column map for the firms file (FIRMES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 */

return [
    'firm_code' => 0,
    'name' => 1,
    'license_number' => 2,
    'title_code' => 3,
    'banner_code' => 4,      // franchise banner, e.g. REM = RE/MAX, IND = independent
    'group_code' => 5,       // observed identical to firm_code in samples
    'firm_number' => 6,      // numeric id, referenced by offices
];
