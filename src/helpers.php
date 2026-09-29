<?php

declare(strict_types=1);

namespace Elegantly\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;
use Closure;

function money(string|int|float|Money|null $value, null|string|Currency $currency = null): ?Money
{
    $currency ??= MoneyServiceProvider::getDefaultCurrency();

    return MoneyParser::parse(
        $value,
        mb_strtoupper((string) $currency)
    );
}

/**
 * Sum the Money values at the given key.
 *
 * @template TValue
 *
 * @param  iterable<array-key, TValue>  $items
 * @param  string|(Closure(TValue $item):?Money)  $key
 */
function sumMoney(
    iterable $items,
    string|Closure $key,
    ?RoundingMode $roundingMode = null,
): ?Money {
    $roundingMode ??= MoneyServiceProvider::getRoundingMode();

    $key = match (true) {
        // @phpstan-ignore-next-line
        is_string($key) => fn ($item): ?Money => data_get($item, $key),
        default => $key,
    };

    $total = null;

    foreach ($items as $item) {
        $money = $key($item);

        if ($money === null) {
            continue;
        }

        $total = $total === null ? $money : $total->plus($money, $roundingMode);
    }

    return $total;
}
