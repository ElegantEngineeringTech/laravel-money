<?php

namespace Finller\Money\Rules;

use Closure;
use Finller\Money\MoneyParser;
use Illuminate\Contracts\Validation\ValidationRule;

class PositiveMoney implements ValidationRule
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
            if (! $money?->isPositive()) {
                $fail('money::validation.money_positive')->translate();
            }
        } catch (\Throwable $th) {
            $fail('money::validation.money_positive')->translate();
        }
    }
}
