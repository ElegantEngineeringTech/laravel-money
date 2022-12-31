<?php

namespace Finller\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;
use Exception;
use Illuminate\Support\Arr;

class MoneyParser
{
    public static function parse(mixed $value, string $currency): ?Money
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Money) {
            return $value;
        }

        if (is_int($value)) {
            return Money::of($value, $currency, null, RoundingMode::HALF_EVEN);
        }

        if (is_float($value)) {
            return Money::of($value, $currency, null, RoundingMode::HALF_EVEN);
        }

        if (is_string($value)) {
            preg_match("/(?<currency>[A-Z]{3})? ?(?<amount>[\d,\.]*)/", $value, $matches);

            $currencyCode = Arr::get($matches, 'currency', $currency);
            $currency = rescue(fn () => Currency::of($currencyCode), $currency);

            $amount = str_replace(',', '', Arr::get($matches, 'amount', '0'));

            return Money::of($amount, $currency, null, RoundingMode::HALF_EVEN);
        }

        throw new Exception('Invalid money value of type '.gettype($value));
    }
}
