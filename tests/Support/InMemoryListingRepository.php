<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Tests\Support;

use Yeevy\CentrisPasserelle\Contracts\ListingRepository;
use Yeevy\CentrisPasserelle\Dto\ListingRecord;

/**
 * Array-backed repository for tests.
 */
class InMemoryListingRepository implements ListingRepository
{
    /** @var array<string, string> */
    public array $hashes = [];

    /** @var array<string, ListingRecord> */
    public array $saved = [];

    /** @var list<string> */
    public array $removed = [];

    public function findDirtyHash(string $mlsNumber): ?string
    {
        return $this->hashes[$mlsNumber] ?? null;
    }

    public function save(ListingRecord $record): void
    {
        $this->hashes[$record->mlsNumber] = $record->dirtyHash;
        $this->saved[$record->mlsNumber] = $record;
    }

    public function activeMlsNumbers(): array
    {
        // PHP casts numeric-string array keys to int — restore strings.
        return array_map(strval(...), array_keys($this->hashes));
    }

    public function remove(string $mlsNumber): void
    {
        unset($this->hashes[$mlsNumber]);
        $this->removed[] = $mlsNumber;
    }
}
