<?php

namespace Elegantly\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Money;

class MoneyParser
{
    public static function parse(
        mixed $value,
        string $currency
    ): ?Money {

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
            return static::parseString($value, $currency);
        }

        return null;
    }

    protected static function parseString(string $value, string $currency): ?Money
    {

        if (blank($value)) {
            return null;
        }

        /**
         * Not found currency or amount will return "" and not null
         */
        preg_match("/(?<currency>[A-Z]{3})? ?(?<amount>[-\d,\.]*)/", $value, $matches);
        /**
         * @var array{ currency: string, amount: string } $matches
         */
        $amount = str_replace(',', '', $matches['amount']);

        if (! $amount && ! $matches['currency']) {
            return null;
        }

        if (! $amount) {
            return Money::of(0, $matches['currency']);
        }

        return Money::of(
            amount: $amount,
            currency: $matches['currency'] ?: $currency,
            roundingMode: RoundingMode::HALF_EVEN
        );
    }
}
