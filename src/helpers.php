<?php

declare(strict_types=1);

namespace Elegantly\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;

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
 * @param  iterable<array-key, mixed>  $items
 */
function sumMoney(
    iterable $items,
    string $key,
    ?RoundingMode $roundingMode = null,
): ?Money {
    $roundingMode ??= MoneyServiceProvider::getRoundingMode();

    $items = collect($items)->where($key, '!=', null);

    if ($first = $items->shift()) {
        // @phpstan-ignore-next-line
        return $items->reduce(
            // @phpstan-ignore-next-line
            fn (Money $total, $item) => $total->plus(data_get($item, $key), $roundingMode),
            data_get($first, $key),
        );
    }

    return null;
}
