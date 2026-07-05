<?php

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Yeevy\CentrisPasserelle\Feed\FlysystemFeedSource;
use Yeevy\CentrisPasserelle\Feed\LocalDirectorySource;

it('returns an existing local directory', function () {
    $directory = __DIR__.'/fixtures/synthetic';

    expect((new LocalDirectorySource($directory))->fetch())->toBe($directory);
});

it('rejects a missing local directory', function () {
    (new LocalDirectorySource('/nonexistent/feed'))->fetch();
})->throws(InvalidArgumentException::class, 'not found');

it('downloads .TXT files from a Flysystem filesystem', function () {
    $remote = new Filesystem(new InMemoryFilesystemAdapter);
    $remote->write('INSCRIPTIONS.TXT', "\"1234567\"\r\n");
    $remote->write('PHOTOS.TXT', "\"1234567\",1\r\n");
    $remote->write('notes.zip', 'not a feed file');

    $local = sys_get_temp_dir().'/centris-feed-test-'.uniqid();

    $directory = (new FlysystemFeedSource($remote, $local))->fetch();

    expect($directory)->toBe($local)
        ->and(file_get_contents($local.'/INSCRIPTIONS.TXT'))->toBe("\"1234567\"\r\n")
        ->and(is_file($local.'/PHOTOS.TXT'))->toBeTrue()
        ->and(is_file($local.'/notes.zip'))->toBeFalse();

    array_map(unlink(...), glob($local.'/*') ?: []);
    rmdir($local);
});
