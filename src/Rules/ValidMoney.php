<?php

declare(strict_types=1);

namespace Elegantly\Money\Rules;

use Brick\Math\BigNumber;
use Brick\Money\AbstractMoney;
use Closure;
use Elegantly\Money\MoneyParser;
use Elegantly\Money\MoneyServiceProvider;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidMoney implements ValidationRule
{
    public function __construct(
        public bool $nullable = true,
        public AbstractMoney|BigNumber|int|string|null $min = null,
        public AbstractMoney|BigNumber|int|string|null $max = null
    ) {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $money = MoneyParser::parse(
            $value,
            MoneyServiceProvider::getDefaultCurrency()
        );

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
        } elseif (! $this->nullable) {
            $fail('money::validation.money')->translate();
        }

    }
}
