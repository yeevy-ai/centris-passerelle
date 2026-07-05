<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Feed;

use RuntimeException;
use ZipArchive;

/**
 * Extracts the snapshot's .TXT files from a ZIP archive, for diffusion
 * agreements that deliver the drop archived. Entries are flattened to
 * their basename (which also guards against zip-slip paths); non-.TXT
 * entries are ignored. Requires ext-zip (see composer suggest).
 */
final class ZipExtractor
{
    /**
     * Extract every .TXT entry into the destination directory and
     * return that directory.
     */
    public function extract(string $archivePath, string $destination): string
    {
        if (! is_file($archivePath)) {
            throw new RuntimeException("Archive not found: {$archivePath}");
        }

        if (! is_dir($destination) && ! mkdir($destination, 0755, true) && ! is_dir($destination)) {
            throw new RuntimeException("Cannot create extraction directory: {$destination}");
        }

        $zip = new ZipArchive;

        if ($zip->open($archivePath) !== true) {
            throw new RuntimeException("Cannot open archive: {$archivePath}");
        }

        try {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $entry = $zip->getNameIndex($index);

                if ($entry === false) {
                    continue;
                }

                $name = basename($entry);

                if (preg_match('/\.txt$/i', $name) !== 1) {
                    continue;
                }

                $contents = $zip->getFromIndex($index);

                if ($contents === false) {
                    throw new RuntimeException("Cannot read archive entry: {$entry}");
                }

                if (file_put_contents($destination.'/'.$name, $contents) === false) {
                    throw new RuntimeException("Cannot write extracted file: {$destination}/{$name}");
                }
            }
        } finally {
            $zip->close();
        }

        return $destination;
    }
}
