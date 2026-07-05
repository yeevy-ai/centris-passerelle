<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Contracts;

/**
 * Where a snapshot comes from: a local directory, an FTP account via
 * Flysystem, or anything else that can produce the feed files locally.
 */
interface FeedSource
{
    /**
     * Make the latest snapshot available locally and return the path
     * to the directory containing the feed files.
     */
    public function fetch(): string;
}
