<?php

declare(strict_types=1);

/*
 * Default column map for the brokers file (MEMBRES.TXT), 0-based.
 *
 * COMMUNITY-OBSERVED positions — verify against your Passerelle
 * documentation and override in your own config if they differ.
 *
 * One row per board member (broker/agent), keyed by broker_code and
 * referenced from listings via the same code.
 */

return [
    'broker_code' => 0,
    'office_code' => 1,
    'license_number' => 2,
    'title_code' => 3,
    'last_name' => 4,
    'first_name' => 5,
    'phone' => 8,
    'phone_secondary' => 9,
    'phone_tertiary' => 10,
    'email' => 11,
    'website_url' => 12,
    'language_code' => 14,   // preferred language: F / A
    'photo_url' => 15,
    'modified_at' => 16,     // YYYY/MM/DD HH:MM:SS
    'company_name' => 17,
    'company_note_fr' => 18,
    'company_note_en' => 19,
    'video_url_fr' => 20,
    'video_url_en' => 21,
    'bio_fr' => 22,
    'bio_en' => 23,
];
