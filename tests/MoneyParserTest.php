<?php

use Brick\Money\Money;
use Finller\Money\MoneyParser;
use Finller\Money\Tests\TestModel;

it('do nothing on null value', function () {
    expect(MoneyParser::parse(null, "EUR"))->toBeNull();
});

it('consider empty string as null value', function () {
    expect(MoneyParser::parse("", "EUR"))->toBeNull();
    expect(MoneyParser::parse(" ", "EUR"))->toBeNull();
});

it('can parse int money', function (string $currency, int $value, int $expected) {
    expect(MoneyParser::parse($value, $currency))->toCost(Money::ofMinor($expected, $currency));
})->with([
    ['EUR', 100, 10000],
    ['EUR', 1123, 112300],
]);

it('can parse float money', function (string $currency, float $value, int $expected) {
    expect(MoneyParser::parse($value, $currency))->toCost(Money::ofMinor($expected, $currency));
})->with([
    ['EUR', 100.10, 10010],
    ['EUR', 11.23, 1123],
    ['EUR', 1235.67, 123567],
]);

it('can parse string money', function (string $currency, string $value, int $expected, ?string $expectedCurency = null) {
    expect(MoneyParser::parse($value, $currency))->toCost(Money::ofMinor($expected, $expectedCurency ?? $currency));
})->with([
    ['EUR', "1", 100],
    ['EUR', "100", 10000],
    ['EUR', "100.10", 10010],
    ['EUR', "EUR 100.10", 10010],
    ['EUR', "USD 100.10", 10010, "USD"],
]);
