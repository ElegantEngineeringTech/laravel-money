<?php

declare(strict_types=1);

use Brick\Money\Money;

use function Elegantly\Money\sumMoney;

it('sums money values at the given key', function () {
    $items = [
        ['price' => Money::of('10.25', 'EUR')],
        ['price' => null],
        ['price' => Money::of('4.75', 'EUR')],
    ];

    $total = sumMoney($items, 'price');

    expect($total)->toCost(Money::of('15.00', 'EUR'));
});

it('returns null when there are no money values to sum', function (array $items) {
    expect(sumMoney($items, 'price'))->toBeNull();
})->with([
    'empty collection' => [[]],
    'only null values' => [[['price' => null]]],
]);
