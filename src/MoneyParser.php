<?php

declare(strict_types=1);

namespace Elegantly\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;

class MoneyParser
{
    public static function parse(
        mixed $value,
        Currency|string $currency
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

    protected static function parseString(
        string $value,
        Currency|string $currency
    ): ?Money {

        $value = str($value)->trim()->replace([',', ' '], '')->value();

        if (blank($value)) {
            return null;
        }

        /**
         * Not found currency or amount will return "" and not null
         */
        preg_match("/(?<currency>[A-Z]{3})? ?(?<amount>[-\d,\.]*)/", $value, $matches);
        /** @var array{ currency: string, amount: string } $matches */
        $amount = $matches['amount'];
        $currency = $matches['currency'] ?: $currency;

        if (blank($amount)) {
            return null;
        }

        return Money::of(
            amount: $amount,
            currency: $currency,
            roundingMode: RoundingMode::HALF_EVEN
        );
    }
}
