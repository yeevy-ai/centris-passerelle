<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Sync;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yeevy\CentrisPasserelle\Contracts\FeedSource;
use Yeevy\CentrisPasserelle\Contracts\ListingRepository;
use Yeevy\CentrisPasserelle\Events\ListingCreated;
use Yeevy\CentrisPasserelle\Events\ListingRemoved;
use Yeevy\CentrisPasserelle\Events\ListingUpdated;
use Yeevy\CentrisPasserelle\Exceptions\ColumnMapMismatch;
use Yeevy\CentrisPasserelle\Parser\ListingsParser;
use Yeevy\CentrisPasserelle\Validation\SnapshotValidator;

/**
 * Synchronizes one full snapshot into consumer storage: validates the
 * file against the column map, upserts changed rows (unchanged dirty
 * hashes are skipped), and reconciles removals — listings present in
 * storage but absent from the snapshot are unpublished, since every
 * drop is a full snapshot, not a delta.
 *
 * When you override column positions, pass a parser AND a validator
 * built from the same map — the defaults use the shipped maps.
 */
final class ListingsSynchronizer
{
    private readonly ListingsParser $parser;

    private readonly SnapshotValidator $validator;

    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly ListingRepository $repository,
        ?ListingsParser $parser = null,
        ?SnapshotValidator $validator = null,
        private readonly ?EventDispatcherInterface $events = null,
        ?LoggerInterface $logger = null,
    ) {
        $this->parser = $parser ?? new ListingsParser;
        $this->validator = $validator ?? new SnapshotValidator;
        $this->logger = $logger ?? new NullLogger;
    }

    /**
     * @param  FeedSource|string  $source  a FeedSource, a snapshot directory, or a listings file path
     *
     * @throws ColumnMapMismatch when the snapshot no longer matches the column map (nothing is written)
     */
    public function sync(FeedSource|string $source, string $filename = 'INSCRIPTIONS.TXT'): SyncResult
    {
        $path = $source instanceof FeedSource ? $source->fetch() : $source;

        if (is_dir($path)) {
            $path = rtrim($path, '/').'/'.$filename;
        }

        $this->validator->validateFile($path);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $removed = 0;
        $seen = [];

        foreach ($this->parser->parseFile($path) as $record) {
            $seen[$record->mlsNumber] = true;

            $knownHash = $this->repository->findDirtyHash($record->mlsNumber);

            if ($knownHash === $record->dirtyHash) {
                $skipped++;

                continue;
            }

            $this->repository->save($record);

            if ($knownHash === null) {
                $created++;
                $this->events?->dispatch(new ListingCreated($record));
            } else {
                $updated++;
                $this->events?->dispatch(new ListingUpdated($record));
            }
        }

        foreach ($this->repository->activeMlsNumbers() as $mlsNumber) {
            if (isset($seen[$mlsNumber])) {
                continue;
            }

            $this->repository->remove($mlsNumber);
            $removed++;
            $this->events?->dispatch(new ListingRemoved($mlsNumber));
        }

        $result = new SyncResult($created, $updated, $skipped, $removed);

        $this->logger->info('Centris snapshot synchronized.', [
            'created' => $result->created,
            'updated' => $result->updated,
            'skipped' => $result->skipped,
            'removed' => $result->removed,
        ]);

        return $result;
    }
}
