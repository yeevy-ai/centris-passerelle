<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Sync;

/**
 * Outcome of one snapshot synchronization.
 */
final readonly class SyncResult
{
    public function __construct(
        public int $created,
        public int $updated,
        public int $skipped,
        public int $removed,
    ) {}

    /**
     * Rows present in the snapshot (created + updated + skipped).
     */
    public function total(): int
    {
        return $this->created + $this->updated + $this->skipped;
    }
}
