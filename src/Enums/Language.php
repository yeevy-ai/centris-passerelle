<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Enums;

/**
 * Language codes as delivered in the feed's bilingual text files.
 * F = français, A = anglais.
 */
enum Language: string
{
    case French = 'F';
    case English = 'A';
}
