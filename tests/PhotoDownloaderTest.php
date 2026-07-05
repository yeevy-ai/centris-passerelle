<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Yeevy\CentrisPasserelle\Dto\PhotoRecord;
use Yeevy\CentrisPasserelle\Exceptions\PhotoDownloadFailed;
use Yeevy\CentrisPasserelle\Photo\PhotoDownloader;

function photoRecord(?string $url, int $sequence = 1): PhotoRecord
{
    return new PhotoRecord(
        mlsNumber: '9999999',
        sequence: $sequence,
        categoryCode: 'FACA',
        descriptionFr: null,
        descriptionEn: null,
        url: $url,
        photoId: (string) $sequence,
        modifiedAt: null,
        dirtyHash: 'hash',
        row: [],
    );
}

function photoDownloader(MockHandler $mock, string $directory): PhotoDownloader
{
    $client = new Client(['handler' => HandlerStack::create($mock)]);

    return new PhotoDownloader($client, new HttpFactory, $directory);
}

function photoTempDir(): string
{
    return sys_get_temp_dir().'/centris-photos-'.uniqid();
}

function cleanPhotoDir(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }

    array_map(unlink(...), glob($dir.'/*') ?: []);
    rmdir($dir);
}

it('downloads a photo to a content-addressed file', function () {
    $dir = photoTempDir();
    $mock = new MockHandler([new Response(200, ['Content-Type' => 'image/jpeg'], 'jpeg-bytes')]);

    $result = photoDownloader($mock, $dir)->download(photoRecord('https://mediaserver.centris.ca/media.ashx?id=1'));

    expect($result->hash)->toBe(hash('sha256', 'jpeg-bytes'))
        ->and($result->path)->toBe($dir.'/'.$result->hash.'.jpg')
        ->and(file_get_contents($result->path))->toBe('jpeg-bytes')
        ->and($result->wasDeduplicated)->toBeFalse();

    cleanPhotoDir($dir);
});

it('deduplicates identical bytes across photos', function () {
    $dir = photoTempDir();
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'image/jpeg'], 'same-bytes'),
        new Response(200, ['Content-Type' => 'image/jpeg'], 'same-bytes'),
    ]);
    $downloader = photoDownloader($mock, $dir);

    $first = $downloader->download(photoRecord('https://example.test/a', 1));
    $second = $downloader->download(photoRecord('https://example.test/b', 2));

    expect($second->path)->toBe($first->path)
        ->and($second->wasDeduplicated)->toBeTrue()
        ->and(glob($dir.'/*'))->toHaveCount(1);

    cleanPhotoDir($dir);
});

it('derives the extension from the content type', function () {
    $dir = photoTempDir();
    $mock = new MockHandler([new Response(200, ['Content-Type' => 'image/png'], 'png-bytes')]);

    $result = photoDownloader($mock, $dir)->download(photoRecord('https://example.test/a'));

    expect($result->path)->toEndWith('.png');

    cleanPhotoDir($dir);
});

it('throws on a failed response', function () {
    $dir = photoTempDir();
    $mock = new MockHandler([new Response(404)]);

    try {
        photoDownloader($mock, $dir)->download(photoRecord('https://example.test/missing'));
        $this->fail('Expected PhotoDownloadFailed');
    } catch (PhotoDownloadFailed $exception) {
        expect($exception->getMessage())->toContain('HTTP 404');
    }

    cleanPhotoDir($dir);
});

it('throws when the record has no URL', function () {
    photoDownloader(new MockHandler([]), photoTempDir())->download(photoRecord(null));
})->throws(PhotoDownloadFailed::class, 'has no URL');

it('skips failures when downloading a batch', function () {
    $dir = photoTempDir();
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'image/jpeg'], 'first'),
        new Response(500),
        new Response(200, ['Content-Type' => 'image/jpeg'], 'third'),
    ]);

    $results = iterator_to_array(photoDownloader($mock, $dir)->downloadAll([
        photoRecord('https://example.test/1', 1),
        photoRecord('https://example.test/2', 2),
        photoRecord('https://example.test/3', 3),
    ]), false);

    expect($results)->toHaveCount(2)
        ->and($results[0]->hash)->toBe(hash('sha256', 'first'))
        ->and($results[1]->hash)->toBe(hash('sha256', 'third'));

    cleanPhotoDir($dir);
});
