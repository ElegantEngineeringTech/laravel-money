<?php

use Elegantly\Money\Rules\ValidMoney;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

it('validate money', function ($amount, bool $expected, ?int $min = null, ?int $max = null, bool $nullable = true) {

    $rule = new ValidMoney(min: $min, max: $max, nullable: $nullable);

    $validator = Validator::make(
        data: [
            'amount' => $amount,
        ],
        rules: [
            'amount' => [$rule],
        ]
    );

    $invalid = $validator->invalid();
    $valid = $validator->valid();

    expect(Arr::has($invalid, 'amount'))->tobe(! $expected);
    expect(Arr::has($valid, 'amount'))->tobe($expected);
})->with([
    [null, true],
    [0, true],
    [10, true],
    [-10, true],
    ['0', true],
    ['10', true],
    ['-10', true],
    ['not money', false, null, null, false],
    ['EUR -10', true, -10, 10],
    ['EUR 0', true, -10, 10],
    ['EUR 100', true],
    ['EUR 10', true, 10, 20],
    ['EUR 20', true, 10, 20],
    ['EUR 1', false, 10, 20],
    ['EUR 100', false, 10, 20],
]);
