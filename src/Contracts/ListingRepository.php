<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Contracts;

use Yeevy\CentrisPasserelle\Dto\ListingRecord;

/**
 * Consumer-owned listing storage. The package never touches a database
 * itself — implement this against your own schema (Eloquent, Doctrine,
 * WordPress posts, plain PDO, …).
 */
interface ListingRepository
{
    /**
     * Dirty hash previously stored for this MLS number, or null when
     * the listing is unknown. Used to skip unchanged rows on upsert.
     */
    public function findDirtyHash(string $mlsNumber): ?string;

    /**
     * Create or update the stored listing, including its dirty hash.
     */
    public function save(ListingRecord $record): void;

    /**
     * MLS numbers of every listing currently published in storage.
     * Diffed against the snapshot to detect removals — each drop is a
     * full snapshot, so absence means sold, expired, or withdrawn.
     *
     * @return list<string>
     */
    public function activeMlsNumbers(): array;

    /**
     * Unpublish a listing that disappeared from the snapshot.
     */
    public function remove(string $mlsNumber): void;
}
