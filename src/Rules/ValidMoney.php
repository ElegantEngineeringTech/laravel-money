<?php

namespace Finller\Money\Rules;

use Closure;
use Finller\Money\MoneyParser;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidMoney implements ValidationRule
{
    public function __construct(public bool $nullable = true, public ?int $min = null, public ?int $max = null)
    {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $money = MoneyParser::parse($value, config('money.default_currency'));

            if (! is_null($value) && is_null($money)) {
                $fail('money::validation.money')->translate();
            }

            if (! $this->nullable && is_null($money)) {
                $fail('money::validation.money')->translate();
            }

            if (! is_null($this->min) && $money->isLessThan($this->min)) {
                $fail('money::validation.money_min')->translate([
                    'value' => $this->min,
                ]);
            }

            if (! is_null($this->max) && $money->isGreaterThan($this->max)) {
                $fail('money::validation.money_max')->translate([
                    'value' => $this->max,
                ]);
            }
        } catch (\Throwable $th) {
            $fail('money::validation.money')->translate();
        }
    }
}
