<?php

namespace Finller\Money\Rules;

use Finller\Money\MoneyParser;
use Illuminate\Contracts\Validation\InvokableRule;

class NegativeOrZeroMoney implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        try {
            $money = MoneyParser::parse($value, config('money.default_currency'));
            if (!$money->isNegativeOrZero()) {
                $fail('money::validation.money_negative_or_zero')->translate();
            }
        } catch (\Throwable $th) {
            $fail('money::validation.money_negative_or_zero')->translate();
        }
    }
}