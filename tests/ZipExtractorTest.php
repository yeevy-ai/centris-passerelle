<?php

use Yeevy\CentrisPasserelle\Feed\LocalDirectorySource;
use Yeevy\CentrisPasserelle\Feed\ZipExtractingSource;
use Yeevy\CentrisPasserelle\Feed\ZipExtractor;

function makeSnapshotArchive(string $dir): string
{
    mkdir($dir, 0755, true);
    $archive = $dir.'/EXTRACT.zip';

    $zip = new ZipArchive;
    $zip->open($archive, ZipArchive::CREATE);
    $zip->addFromString('INSCRIPTIONS.TXT', "\"1234567\"\r\n");
    $zip->addFromString('nested/REMARQUES.TXT', "\"1234567\",1,\"F\",1,,,\"texte\"\r\n");
    $zip->addFromString('notes.dat', 'not a feed file');
    $zip->close();

    return $archive;
}

function removeDir(string $dir): void
{
    array_map(unlink(...), glob($dir.'/*') ?: []);
    rmdir($dir);
}

it('extracts .TXT entries flattened into the destination', function () {
    $dir = sys_get_temp_dir().'/centris-zip-'.uniqid();
    $archive = makeSnapshotArchive($dir);

    $destination = (new ZipExtractor)->extract($archive, $dir.'/out');

    expect(file_get_contents($destination.'/INSCRIPTIONS.TXT'))->toBe("\"1234567\"\r\n")
        ->and(is_file($destination.'/REMARQUES.TXT'))->toBeTrue()
        ->and(is_file($destination.'/notes.dat'))->toBeFalse();

    removeDir($destination);
    removeDir($dir);
});

it('rejects a missing archive', function () {
    (new ZipExtractor)->extract('/nonexistent/EXTRACT.zip', sys_get_temp_dir());
})->throws(RuntimeException::class, 'not found');

it('extracts archives in place when decorating a source', function () {
    $dir = sys_get_temp_dir().'/centris-zip-'.uniqid();
    makeSnapshotArchive($dir);

    $source = new ZipExtractingSource(new LocalDirectorySource($dir));

    expect($source->fetch())->toBe($dir)
        ->and(is_file($dir.'/INSCRIPTIONS.TXT'))->toBeTrue()
        ->and(is_file($dir.'/REMARQUES.TXT'))->toBeTrue();

    removeDir($dir);
});
