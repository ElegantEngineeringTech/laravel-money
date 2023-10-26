<?php

namespace Finller\Money\Rules;

use Closure;
use Finller\Money\MoneyParser;
use Illuminate\Contracts\Validation\ValidationRule;

class NegativeMoney implements ValidationRule
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
            if (! $money?->isNegative()) {
                $fail('money::validation.money_negative')->translate();
            }
        } catch (\Throwable $th) {
            $fail('money::validation.money_negative')->translate();
        }
    }
}
