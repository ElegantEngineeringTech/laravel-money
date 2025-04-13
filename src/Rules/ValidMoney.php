<?php

declare(strict_types=1);

namespace Elegantly\Money\Rules;

use Closure;
use Elegantly\Money\MoneyParser;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidMoney implements ValidationRule
{
    public function __construct(
        public bool $nullable = true,
        public ?int $min = null,
        public ?int $max = null
    ) {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var string $defaultCurrency */
        $defaultCurrency = config('money.default_currency');
        $money = MoneyParser::parse($value, $defaultCurrency);

        if (! $this->nullable && $money === null) {
            $fail('money::validation.money')->translate();
        }

        if ($money) {
            if (
                $this->min !== null &&
                $money->isLessThan($this->min)
            ) {
                $fail('money::validation.money_min')->translate([
                    'value' => $this->min,
                ]);
            }

            if (
                $this->max !== null &&
                $money->isGreaterThan($this->max)
            ) {
                $fail('money::validation.money_max')->translate([
                    'value' => $this->max,
                ]);
            }
        }
    }
}
