<?php

use Brick\Money\Money;
use Elegantly\Money\MoneyParser;

it('do nothing on null value', function () {
    expect(MoneyParser::parse(null, 'EUR'))->toBeNull();
});

it('consider empty string as null value', function () {
    expect(MoneyParser::parse('', 'EUR'))->toBeNull();
    expect(MoneyParser::parse(' ', 'EUR'))->toBeNull();
});

it('can parse int money', function (string $currency, int $value, int $expected) {
    expect(MoneyParser::parse($value, $currency)->getAmount()->toFloat())->toEqual(Money::ofMinor($expected, $currency)->getAmount()->toFloat());
})->with([
    ['EUR', 100, 10000],
    ['EUR', -100, -10000],
    ['EUR', 1123, 112300],
    ['EUR', -1123, -112300],
]);

it('can parse float money', function (string $currency, float $value, int $expected) {
    expect(MoneyParser::parse($value, $currency)->getAmount()->toFloat())->toEqual(Money::ofMinor($expected, $currency)->getAmount()->toFloat());
})->with([
    ['EUR', 100.10, 10010],
    ['EUR', -100.10, -10010],
    ['EUR', 11.23, 1123],
    ['EUR', -11.23, -1123],
    ['EUR', 1235.67, 123567],
    ['EUR', -1235.67, -123567],
]);

it('can parse string money', function (string $currency, string $value, int $expected, ?string $expectedCurrency = null) {
    expect(MoneyParser::parse($value, $currency)->getAmount()->toFloat())->toEqual(Money::ofMinor($expected, $expectedCurrency ?? $currency)->getAmount()->toFloat());
})->with([
    ['EUR', '0', 0],
    ['EUR', '1', 100],
    ['EUR', '100', 10000],
    ['EUR', '-100', -10000],
    ['EUR', '100.10', 10010],
    ['EUR', '-100.10', -10010],
    ['EUR', 'EUR 100.10', 10010],
    ['EUR', 'EUR -100.10', -10010],
    ['EUR', 'USD 100.10', 10010, 'USD'],
    ['EUR', 'USD -100.10', -10010, 'USD'],
]);
