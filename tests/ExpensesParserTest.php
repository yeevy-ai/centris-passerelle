<?php

use Yeevy\CentrisPasserelle\Parser\ExpensesParser;

it('parses the synthetic expenses fixture', function () {
    $records = iterator_to_array(
        (new ExpensesParser)->parseFile(__DIR__.'/fixtures/synthetic/expenses.txt'),
        false,
    );

    expect($records)->toHaveCount(2);

    [$municipalTax, $energy] = $records;

    expect($municipalTax->typeCode)->toBe('TAXMUN')
        ->and($municipalTax->amount)->toBe(5443)
        ->and($municipalTax->year)->toBe(2026)
        ->and($municipalTax->frequencyCode)->toBe('A')
        ->and($energy->typeCode)->toBe('ENER')
        ->and($energy->year)->toBeNull()
        ->and($energy->descriptionFr)->toBe('Électricité')
        ->and($energy->descriptionEn)->toBe('Electricity');
});
