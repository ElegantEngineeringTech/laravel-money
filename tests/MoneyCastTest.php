<?php

declare(strict_types=1);

use Brick\Money\Money;
use Elegantly\Money\Tests\TestModel;

it('can cast integer value to money', function ($currency, $price, $expected) {
    $model = new TestModel([
        'currency' => $currency,
        'price' => $price,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe($expected);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe($currency);
})->with([
    ['EUR', 0, 0.0],
    ['EUR', 1, 1.0],
    ['EUR', -1, -1.0],
    ['EUR', 123, 123.0],
    ['EUR', -123, -123.0],
    ['EUR', 1234, 1234.0],
    ['EUR', -1234, -1234.0],
    //
    ['USD', 0, 0.0],
    ['GBP', 0, 0.0],
]);

it('can cast money represented as float', function ($currency, $price, $expected) {
    $model = new TestModel([
        'currency' => $currency,
        'price' => $price,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe($expected);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe($currency);
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
    //
    ['USD', 0.0, 0.0],
    ['GBP', 0.0, 0.0],
]);

it('can cast money represented as string without currency', function ($currency, $price, $expected) {
    $model = new TestModel([
        'currency' => $currency,
        'price' => $price,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe($expected);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe($currency);
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
]);

it('can cast money represented as string with currency', function ($price, $expected, $currency) {
    $model = new TestModel([
        'price' => $price,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe($expected);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe($currency);
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
    // ignore `,`
    ['GBP 1,234.56', 1234.56, 'GBP'],
    ['USD -1,234.56', -1234.56, 'USD'],
]);
