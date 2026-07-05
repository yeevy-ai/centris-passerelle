<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Feed;

use InvalidArgumentException;
use Yeevy\CentrisPasserelle\Contracts\FeedSource;

/**
 * Snapshot files already on local disk — a cron-managed drop folder,
 * an extracted archive, or test fixtures.
 */
final class LocalDirectorySource implements FeedSource
{
    public function __construct(
        private readonly string $directory,
    ) {}

    public function fetch(): string
    {
        if (! is_dir($this->directory)) {
            throw new InvalidArgumentException("Feed directory not found: {$this->directory}");
        }

        return $this->directory;
    }
}
