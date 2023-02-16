<?php

namespace Finller\Money\Rules;

use Closure;
use Finller\Money\MoneyParser;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidMoney implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $money = MoneyParser::parse($value, config('money.default_currency'));
        } catch (\Throwable $th) {
            $fail('money::validation.money')->translate();
        }
    }
}
