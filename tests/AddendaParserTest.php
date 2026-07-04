<?php

use Yeevy\CentrisPasserelle\Enums\Language;
use Yeevy\CentrisPasserelle\Parser\AddendaParser;

it('parses the synthetic addenda fixture', function () {
    $records = iterator_to_array(
        (new AddendaParser)->parseFile(__DIR__.'/fixtures/synthetic/addenda.txt'),
        false,
    );

    expect($records)->toHaveCount(4)
        ->and($records[0]->language)->toBe(Language::French)
        ->and($records[0]->addendumNumber)->toBe(1)
        ->and($records[0]->partNumber)->toBe(1)
        ->and($records[1]->partNumber)->toBe(2)
        ->and($records[2]->language)->toBe(Language::English);
});

it('reassembles chunked addendum text by part number', function () {
    $records = iterator_to_array(
        (new AddendaParser)->parseFile(__DIR__.'/fixtures/synthetic/addenda.txt'),
        false,
    );

    $french = array_filter($records, fn ($r) => $r->language === Language::French);
    usort($french, fn ($a, $b) => $a->partNumber <=> $b->partNumber);
    $text = implode(' ', array_map(fn ($r) => $r->text, $french));

    expect($text)->toBe("Bienvenue sur la Rue Exemple, à quelques pas du parc et de l'école primaire du quartier.");
});
