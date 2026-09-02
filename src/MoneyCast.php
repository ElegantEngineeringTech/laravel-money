<?php

declare(strict_types=1);

namespace Elegantly\Money;

use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom Eloquent attribute cast for handling monetary values.
 *
 * @implements CastsAttributes<null|Money, array<string, int|string>>
 */
class MoneyCast implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * @param  ?string  $currency  The currency code or the model attribute storing the currency code.
     *                             Usage examples:
     *                             - MoneyCast::class.':currency' (Currency stored in a model attribute)
     *                             - MoneyCast::class.':EUR' (Fixed currency set to EUR)
     */
    public function __construct(
        protected ?string $currency = null
    ) {
        // No initialization required
    }

    /**
     * @param  string  $currency  The currency code or the model attribute storing the currency code.
     */
    public static function of(string $currency): string
    {
        return static::class.':'.$currency;
    }

    /**
     * @return ($currency is null ? false : bool)
     */
    public function isCurrencyCode(?string $currency): bool
    {
        return $currency && mb_strlen($currency) === 3;
    }

    /**
     * Retrieve the currency code from the model's attributes.
     *
     * @param  array<string, mixed>  $attributes  The model's attributes.
     * @return ?string The currency code, if available.
     */
    protected function getCurrencyAttributeValue(?string $currency, array $attributes): ?string
    {
        if ($currency === null) {
            return null;
        }

        /** @var ?string $value */
        $value = $attributes[$currency] ?? null;

        return $value;
    }

    /**
     * Get the currency instance.
     *
     * @param  array<string, mixed>  $attributes  The model's attributes.
     * @return Currency The currency instance.
     */
    protected function getCurrencyValue(array $attributes): Currency
    {
        $default = MoneyServiceProvider::getDefaultCurrency();

        if ($currency = $this->getCurrencyAttributeValue($this->currency, $attributes)) {
            return Currency::of($currency);
        }

        if ($this->currency && $this->isCurrencyCode($this->currency)) {
            return Currency::of($this->currency);
        }

        return Currency::of($default);
    }

    /**
     * Cast the given value into a Money instance.
     *
     * Money is stored as an integer representing minor units (e.g., cents).
     *
     * @param  ?int  $value  The stored value.
     * @return ?Money The corresponding Money instance or null.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        return Money::ofMinor($value, $this->getCurrencyValue($attributes));
    }

    /**
     * Prepare the given value for database storage.
     *
     * Money is stored as an integer in minor units (e.g., cents), ensuring compatibility.
     *
     * String representations of money are parsed in various formats, such as:
     * - "USD 1000.00" => $1,000.00
     * - "USD 1000" => $1000
     * - "USD 10.00" => $10.00
     * - "USD 1,000" => $1000 (commas are ignored)
     *
     * @param  null|int|float|string|Money  $value  The monetary value to store.
     * @return array<string, int|string|null> The formatted data for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): null|int|array
    {
        $money = MoneyParser::parse($value, $this->getCurrencyValue($attributes));

        if ($money === null) {
            return null;
        }

        if ($this->currency && ! $this->isCurrencyCode($this->currency)) {
            return [
                $key => $money->getMinorAmount()->toInt(),
                $this->currency => $money->getCurrency()->getCurrencyCode(),
            ];
        }

        return $money->getMinorAmount()->toInt();
    }

    /**
     * Serialize the Money instance into a float for API responses.
     *
     * @param  ?Money  $value  The Money instance.
     * @param  array<string, mixed>  $attributes  The model's attributes.
     * @return ?float The monetary value as a float or null.
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        if ($value === null) {
            return null;
        }

        return $value->getAmount()->toFloat();
    }
}
