<?php

declare(strict_types=1);

use Elegantly\Money\MoneyParser;

it('parses blank value as null', function ($value) {
    expect(MoneyParser::parse($value, 'EUR'))->toBeNull();
})->with([
    [null],
    [''],
    [' '],
]);

it('can parse int money', function (string $currency, int $value, float $expected) {
    $parsed = MoneyParser::parse($value, $currency);

    expect($parsed->getAmount()->toFloat())->toEqual($expected);
    expect($parsed->getCurrency()->getCurrencyCode())->toEqual($currency);
})->with([
    ['EUR', 0, 0.0],
    ['EUR', 1, 1.0],
    ['EUR', -1, -1.0],
    ['EUR', 123, 123.0],
    ['EUR', -123, -123.0],
    ['EUR', 1234, 1234.0],
    ['EUR', -1234, -1234.0],
]);

it('can parse float money', function (string $currency, float $value, float $expected) {
    $parsed = MoneyParser::parse($value, $currency);

    expect($parsed->getAmount()->toFloat())->toEqual($expected);
    expect($parsed->getCurrency()->getCurrencyCode())->toEqual($currency);
})->with([
    ['EUR', 0.0, 0.0],
    ['EUR', 1.0, 1.0],
    ['EUR', -1.0, -1.0],
    ['EUR', 123.0, 123.00],
    ['EUR', -123.0, -123.0],
    ['EUR', 1234.0, 1234.0],
    ['EUR', -1234.0, -1234.0],
    ['EUR', 1234.5, 1234.5],
    ['EUR', -1234.5, -1234.5],
    ['EUR', 1234.56, 1234.56],
    ['EUR', -1234.56, -1234.56],
]);

it('can parse string money', function (string $currency, string $value, float $expected) {
    $parsed = MoneyParser::parse($value, $currency);

    expect($parsed->getAmount()->toFloat())->toEqual($expected);
    expect($parsed->getCurrency()->getCurrencyCode())->toEqual($currency);
})->with([
    ['EUR', '0.0', 0.0],
    ['EUR', '1.0', 1.0],
    ['EUR', '-1.0', -1.0],
    ['EUR', '123.0', 123.0],
    ['EUR', '-123.0', -123.0],
    ['EUR', '1234.0', 1234.0],
    ['EUR', '-1234.0', -1234.0],
    ['EUR', '1234.5', 1234.5],
    ['EUR', '-1234.5', -1234.5],
    ['EUR', '1234.56', 1234.56],
    ['EUR', '-1234.56', -1234.56],
    // ignore ` `
    ['EUR', '-1 234.56', -1234.56],
    ['EUR', '1,234.56', 1234.56],
    // ignore `,`
    ['EUR', '1,234.56', 1234.56],
    ['EUR', '-1,234.56', -1234.56],
]);

it('can parse string money with currency', function (string $value, float $expected, ?string $expectedCurrency = null) {
    $parsed = MoneyParser::parse($value, 'USD'); // check that the string currency override the default currency

    expect($parsed->getAmount()->toFloat())->toEqual($expected);
    expect($parsed->getCurrency()->getCurrencyCode())->toEqual($expectedCurrency);
})->with([
    ['EUR 0.0', 0.0, 'EUR'],
    ['EUR 1.0', 1.0, 'EUR'],
    ['EUR -1.0', -1.0, 'EUR'],
    ['EUR 123.0', 123.00, 'EUR'],
    ['EUR -123.0', -123.0, 'EUR'],
    ['EUR 1234.0', 1234.0, 'EUR'],
    ['EUR -1234.0', -1234.0, 'EUR'],
    ['EUR 1234.5', 1234.5, 'EUR'],
    ['EUR -1234.5', -1234.5, 'EUR'],
    ['EUR 1234.56', 1234.56, 'EUR'],
    ['EUR -1234.56', -1234.56, 'EUR'],
    // ignore ` `
    ['GBP 1 234.56', 1234.56, 'GBP'],
    ['USD -1 234.56', -1234.56, 'USD'],
    // ignore `,`
    ['GBP 1,234.56', 1234.56, 'GBP'],
    ['USD -1,234.56', -1234.56, 'USD'],
]);
