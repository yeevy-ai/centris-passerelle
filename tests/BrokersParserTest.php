<?php

use Yeevy\CentrisPasserelle\Enums\Language;
use Yeevy\CentrisPasserelle\Parser\BrokersParser;

it('parses the synthetic brokers fixture', function () {
    $records = iterator_to_array(
        (new BrokersParser)->parseFile(__DIR__.'/fixtures/synthetic/brokers.txt'),
        false,
    );

    expect($records)->toHaveCount(1);

    $broker = $records[0];

    expect($broker->brokerCode)->toBe('00001')
        ->and($broker->officeCode)->toBe('XXX001')
        ->and($broker->lastName)->toBe('Courtier')
        ->and($broker->firstName)->toBe('Exemple')
        ->and($broker->phone)->toBe('5145550100')
        ->and($broker->email)->toBe('courtier@exemple.test')
        ->and($broker->language)->toBe(Language::French)
        ->and($broker->modifiedAt?->format('Y-m-d'))->toBe('2026-01-23')
        ->and($broker->companyNoteFr)->toBe("Société par actions d'un courtier")
        ->and($broker->bioFr)->toBe("Courtier d'expérience à Montréal.");
});
