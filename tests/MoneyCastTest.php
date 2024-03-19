<?php

use Brick\Money\Money;
use Finller\Money\Tests\TestModel;

it('can cast money represented as integer', function () {
    $model = new TestModel([
        'currency' => 'EUR',
        'price' => 1234,
        'price_default_currency' => 9876,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getMinorAmount()->toInt())->toBe(123400);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe('EUR');

    expect($model->price_default_currency)->toBeInstanceOf(Money::class);
    expect($model->price_default_currency->getMinorAmount()->toInt())->toBe(987600);
    expect($model->price_default_currency->getCurrency()->getCurrencyCode())->toBe(config('money.default_currency'));
});

it('can cast money represented as float', function () {
    $model = new TestModel([
        'currency' => 'EUR',
        'price' => 1234.56,
        'price_default_currency' => 9876.54,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe(1234.56);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe('EUR');

    expect($model->price_default_currency)->toBeInstanceOf(Money::class);
    expect($model->price_default_currency->getAmount()->toFloat())->toBe(9876.54);
    expect($model->price_default_currency->getCurrency()->getCurrencyCode())->toBe(config('money.default_currency'));
});

it('can cast money represented as serialized string', function ($currency, $price, $expected) {
    $model = new TestModel([
        'currency' => $currency,
        'price' => $price,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe($expected);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe($currency);
})->with([
    ['EUR', 'EUR 1,234.56', 1234.56],
    ['EUR', 'EUR 1234.56', 1234.56],
    ['EUR', '1234', 1234.0],
    ['EUR', 'EUR 1,234', 1234.0], // ignore ","
]);

it('can cast money represented as serialized string without currency attribute', function ($currency, $price, $expected) {
    $model = new TestModel([
        'price' => $price,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe($expected);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe($currency);
})->with([
    ['EUR', 'EUR 1,234.56', 1234.56],
    ['EUR', 'EUR 1234.56', 1234.56],
    ['USD', '1234', 1234.0],
    ['GBP', 'GBP 1,234', 1234.0], // ignore ","
]);
