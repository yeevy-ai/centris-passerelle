<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Photo;

use Generator;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yeevy\CentrisPasserelle\Dto\PhotoRecord;
use Yeevy\CentrisPasserelle\Exceptions\PhotoDownloadFailed;

/**
 * Downloads listing photos through any PSR-18 client. Files are
 * content-addressed ({sha256}.{ext}) inside the target directory, so
 * identical bytes — re-drops, co-listings sharing media — are stored
 * exactly once.
 */
final class PhotoDownloader
{
    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly string $directory,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger;
    }

    /**
     * @throws PhotoDownloadFailed
     */
    public function download(PhotoRecord $photo): DownloadedPhoto
    {
        if ($photo->url === null) {
            throw new PhotoDownloadFailed(
                "Photo {$photo->mlsNumber}/{$photo->sequence} has no URL."
            );
        }

        try {
            $response = $this->client->sendRequest(
                $this->requestFactory->createRequest('GET', $photo->url),
            );
        } catch (ClientExceptionInterface $exception) {
            throw new PhotoDownloadFailed(
                "Download failed for {$photo->url}: {$exception->getMessage()}",
                previous: $exception,
            );
        }

        if ($response->getStatusCode() !== 200) {
            throw new PhotoDownloadFailed(
                "Download failed for {$photo->url}: HTTP {$response->getStatusCode()}"
            );
        }

        $bytes = (string) $response->getBody();

        if ($bytes === '') {
            throw new PhotoDownloadFailed("Empty response for {$photo->url}");
        }

        if (! is_dir($this->directory) && ! mkdir($this->directory, 0755, true) && ! is_dir($this->directory)) {
            throw new PhotoDownloadFailed("Cannot create photo directory: {$this->directory}");
        }

        $hash = hash('sha256', $bytes);
        $path = $this->directory.'/'.$hash.'.'.$this->extension($response->getHeaderLine('Content-Type'));

        if (is_file($path)) {
            return new DownloadedPhoto($photo, $path, $hash, wasDeduplicated: true);
        }

        if (file_put_contents($path, $bytes) === false) {
            throw new PhotoDownloadFailed("Cannot write photo: {$path}");
        }

        return new DownloadedPhoto($photo, $path, $hash, wasDeduplicated: false);
    }

    /**
     * Download many photos lazily; failures are logged and skipped so
     * one broken URL never aborts the batch.
     *
     * @param  iterable<PhotoRecord>  $photos
     * @return Generator<int, DownloadedPhoto>
     */
    public function downloadAll(iterable $photos): Generator
    {
        foreach ($photos as $photo) {
            try {
                yield $this->download($photo);
            } catch (PhotoDownloadFailed $exception) {
                $this->logger->warning('Skipping photo download.', [
                    'mls_number' => $photo->mlsNumber,
                    'sequence' => $photo->sequence,
                    'reason' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function extension(string $contentType): string
    {
        return match (strtolower(trim(explode(';', $contentType)[0]))) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }
}
