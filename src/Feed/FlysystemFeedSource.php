<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Feed;

use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use RuntimeException;
use Yeevy\CentrisPasserelle\Contracts\FeedSource;

/**
 * Downloads the snapshot's .TXT files from a Flysystem filesystem to a
 * local directory. Pair with league/flysystem-ftp or
 * league/flysystem-sftp-v3 for the Passerelle FTP account (see
 * composer suggest — league/flysystem is not installed by default).
 */
final class FlysystemFeedSource implements FeedSource
{
    public function __construct(
        private readonly FilesystemOperator $filesystem,
        private readonly string $localDirectory,
        private readonly string $remoteDirectory = '',
    ) {}

    public function fetch(): string
    {
        if (! is_dir($this->localDirectory) && ! mkdir($this->localDirectory, 0755, true) && ! is_dir($this->localDirectory)) {
            throw new RuntimeException("Cannot create local feed directory: {$this->localDirectory}");
        }

        foreach ($this->filesystem->listContents($this->remoteDirectory) as $item) {
            if (! $item instanceof FileAttributes) {
                continue;
            }

            $name = basename($item->path());

            if (preg_match('/\.txt$/i', $name) !== 1) {
                continue;
            }

            $remote = $this->filesystem->readStream($item->path());
            $local = fopen($this->localDirectory.'/'.$name, 'wb');

            if ($local === false) {
                throw new RuntimeException("Cannot write local feed file: {$this->localDirectory}/{$name}");
            }

            stream_copy_to_stream($remote, $local);
            fclose($local);
            fclose($remote);
        }

        return $this->localDirectory;
    }
}
