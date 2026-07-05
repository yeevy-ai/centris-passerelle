<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Feed;

use Yeevy\CentrisPasserelle\Contracts\FeedSource;

/**
 * Decorates any FeedSource: after fetching, every .zip in the snapshot
 * directory is extracted in place, so archived drops parse exactly
 * like plain ones.
 *
 *     $source = new ZipExtractingSource(new FlysystemFeedSource(...));
 */
final class ZipExtractingSource implements FeedSource
{
    private readonly ZipExtractor $extractor;

    public function __construct(
        private readonly FeedSource $inner,
        ?ZipExtractor $extractor = null,
    ) {
        $this->extractor = $extractor ?? new ZipExtractor;
    }

    public function fetch(): string
    {
        $directory = $this->inner->fetch();

        foreach (glob($directory.'/*.[Zz][Ii][Pp]') ?: [] as $archive) {
            $this->extractor->extract($archive, $directory);
        }

        return $directory;
    }
}
