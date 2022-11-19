<?php

use Brick\Money\Money;
use Finller\Money\Tests\TestModel;

it('can prepare money represented as integer', function () {
    $model = new TestModel([
        'currency' => 'EUR',
        'price' => 1234,
        'price_default_currency' => 9876,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getMinorAmount()->toInt())->toBe(1234);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe("EUR");

    expect($model->price_default_currency)->toBeInstanceOf(Money::class);
    expect($model->price_default_currency->getMinorAmount()->toInt())->toBe(9876);
    expect($model->price_default_currency->getCurrency()->getCurrencyCode())->toBe(config('money.default_currency'));
});


it('can prepare money represented as float', function () {
    $model = new TestModel([
        'currency' => 'EUR',
        'price' => 1234.56,
        'price_default_currency' => 9876.54,
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe(1234.56);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe("EUR");

    expect($model->price_default_currency)->toBeInstanceOf(Money::class);
    expect($model->price_default_currency->getAmount()->toFloat())->toBe(9876.54);
    expect($model->price_default_currency->getCurrency()->getCurrencyCode())->toBe(config('money.default_currency'));
});


it('can prepare money represented as serialized string', function () {
    $model = new TestModel([
        'currency' => 'EUR',
        'price' => "EUR 1,234.56",
        'price_default_currency' => (string) Money::of(9876.54, 'USD'),
    ]);

    expect($model->price)->toBeInstanceOf(Money::class);
    expect($model->price->getAmount()->toFloat())->toBe(1234.56);
    expect($model->price->getCurrency()->getCurrencyCode())->toBe("EUR");

    expect($model->price_default_currency)->toBeInstanceOf(Money::class);
    expect($model->price_default_currency->getAmount()->toFloat())->toBe(9876.54);
    expect($model->price_default_currency->getCurrency()->getCurrencyCode())->toBe('USD');
});
