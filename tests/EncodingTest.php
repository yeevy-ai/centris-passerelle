<?php

use Yeevy\CentrisPasserelle\Support\Encoding;

it('converts Windows-1252 to UTF-8', function () {
    // "Réfrigérateur" with é as the Windows-1252 single byte 0xE9
    expect(Encoding::toUtf8("R\xE9frig\xE9rateur"))->toBe('Réfrigérateur');
});

it('maps Windows-1252-specific bytes to real glyphs', function () {
    // 0x92 (right single quote) and 0x9C (œ) differ between
    // Windows-1252 and ISO-8859-1.
    expect(Encoding::toUtf8("\x92"))->toBe('’')
        ->and(Encoding::toUtf8("\x9C"))->toBe('œ');
});

it('leaves ASCII untouched', function () {
    $ascii = '"9999999",,"00001","XXX001",499000';

    expect(Encoding::toUtf8($ascii))->toBe($ascii);
});

it('converts the synthetic fixture as delivered by the feed', function () {
    $raw = file_get_contents(__DIR__.'/fixtures/synthetic/listings.txt');

    expect($raw)->not->toBeFalse();

    // Delivered encoding is Windows-1252 with CRLF line endings.
    expect(mb_check_encoding($raw, 'UTF-8'))->toBeFalse()
        ->and($raw)->toContain("\r\n");

    $utf8 = Encoding::toUtf8($raw);

    expect(mb_check_encoding($utf8, 'UTF-8'))->toBeTrue()
        ->and($utf8)->toContain('Réfrigérateur, cuisinière');
});
