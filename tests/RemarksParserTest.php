<?php

use Yeevy\CentrisPasserelle\Enums\Language;
use Yeevy\CentrisPasserelle\Parser\RemarksParser;

it('parses the synthetic remarks fixture', function () {
    $records = iterator_to_array(
        (new RemarksParser)->parseFile(__DIR__.'/fixtures/synthetic/remarks.txt'),
        false,
    );

    expect($records)->toHaveCount(2);

    [$french, $english] = $records;

    expect($french->mlsNumber)->toBe('9999999')
        ->and($french->remarkNumber)->toBe(1)
        ->and($french->language)->toBe(Language::French)
        ->and($french->partNumber)->toBe(1)
        ->and($french->text)->toBe("Propriété rénovée près de l'école et du fleuve.")
        ->and($english->language)->toBe(Language::English)
        ->and($english->text)->toBe('Renovated property near the school and the river.');
});

it('skips remark rows without an MLS number', function () {
    $records = iterator_to_array(
        (new RemarksParser)->parseString(",1,\"F\",1,,,\"orphan\"\r\n\"1234567\",1,\"F\",1,,,\"kept\"\r\n"),
        false,
    );

    expect($records)->toHaveCount(1)
        ->and($records[0]->text)->toBe('kept');
});
