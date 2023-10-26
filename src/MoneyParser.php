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
        if ($value === null) {
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
            if (trim($value) === '') {
                return null;
            }

            $matches = static::parseFromString($value);

            if (empty($matches)) {
                return null;
            }

            /** @var string $parsedCurrencyCode */
            $parsedCurrencyCode = Arr::get($matches, 'currency', $currency);
            $currencyInstance = Currency::of($parsedCurrencyCode);

            /** @var string $amount */
            $amount = str_replace(',', '', Arr::get($matches, 'amount', '0'));

            return Money::of($amount, $currencyInstance, null, RoundingMode::HALF_EVEN);
        }

        throw new Exception('Invalid money value of type '.gettype($value));
    }

    protected static function parseFromString(string $value): array
    {
        // not found currency or amount will return "" and not null
        preg_match("/(?<currency>[A-Z]{3})? ?(?<amount>[-\d,\.]*)/", $value, $matches);

        return array_filter($matches);
    }
}
