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
        Currency|string $currency,
        ?RoundingMode $roundingMode = null
    ): ?Money {
        $roundingMode ??= config('money.rounding_mode', RoundingMode::HalfUp);
        /** @var RoundingMode $roundingMode */
        if ($value === null) {
            return null;
        }

        if ($value instanceof Money) {
            return $value;
        }

        if (is_int($value)) {
            return Money::of(
                amount: $value,
                currency: $currency,
                roundingMode: $roundingMode,
            );
        }

        if (is_float($value)) {
            return Money::of(
                amount: (string) $value,
                currency: $currency,
                roundingMode: $roundingMode,
            );
        }

        if (is_string($value)) {
            return static::parseString(
                $value,
                $currency,
                $roundingMode
            );
        }

        return null;
    }

    protected static function parseString(
        string $value,
        Currency|string $currency,
        ?RoundingMode $roundingMode = null
    ): ?Money {
        $roundingMode ??= config('money.rounding_mode', RoundingMode::HalfUp);
        /** @var RoundingMode $roundingMode */
        $value = str($value)->trim()->replace([',', ' '], '')->value();

        if (blank($value)) {
            return null;
        }

        /**
         * Not found currency or amount will return "" and not null
         */
        preg_match("/(?<currency>[A-Z]{3})? ?(?<amount>[-\d,\.]*)/", $value, $matches);

        /**
         * @var array{0: string, currency: string, 1: string, amount: string, 2: string} $matches
         */
        $amount = $matches['amount'];
        $currency = $matches['currency'] ?: $currency;

        if (blank($amount)) {
            return null;
        }

        return Money::of(
            amount: $amount,
            currency: $currency,
            roundingMode: $roundingMode,
        );
    }
}
