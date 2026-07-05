<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Photo;

use Yeevy\CentrisPasserelle\Dto\PhotoRecord;

/**
 * Outcome of one photo download. Files are content-addressed, so
 * wasDeduplicated means identical bytes were already on disk and no
 * new file was written.
 */
final readonly class DownloadedPhoto
{
    public function __construct(
        public PhotoRecord $photo,
        public string $path,
        public string $hash,
        public bool $wasDeduplicated,
    ) {}
}
