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
     * @param  ?string  $currencyOrAttribute  The currency code or the model attribute storing the currency code.
     *                                        Usage examples:
     *                                        - MoneyCast::class.':currency' (Currency stored in a model attribute)
     *                                        - MoneyCast::class.':EUR' (Fixed currency set to EUR)
     */
    public function __construct(
        protected ?string $currencyOrAttribute = null
    ) {
        // No initialization required
    }

    public static function of(string $currencyOrAttribute): string
    {
        return static::class.':'.$currencyOrAttribute;
    }

    /**
     * @param  array<string, mixed>  $attributes  The model's attributes.
     */
    public function isCurrencyAttribute(array $attributes): bool
    {
        return $this->currencyOrAttribute && array_key_exists($this->currencyOrAttribute, $attributes);
    }

    public function isCurrencyCode(): bool
    {
        return $this->currencyOrAttribute && mb_strlen($this->currencyOrAttribute) === 3;
    }

    /**
     * Retrieve the currency code from the model's attributes.
     *
     * @param  array<string, mixed>  $attributes  The model's attributes.
     * @return ?string The currency code, if available.
     */
    protected function getCurrencyAttribute(array $attributes): ?string
    {
        if ($this->isCurrencyAttribute($attributes)) {
            /** @var ?string $currency */
            $currency = $attributes[$this->currencyOrAttribute];

            return $currency;
        }

        return null;
    }

    /**
     * Get the currency instance.
     *
     * @param  array<string, mixed>  $attributes  The model's attributes.
     * @return Currency The currency instance.
     */
    protected function getCurrency(array $attributes): Currency
    {
        /** @var string $default */
        $default = config('money.default_currency');

        if ($currency = $this->getCurrencyAttribute($attributes)) {
            return Currency::of($currency);
        }

        if ($this->currencyOrAttribute && $this->isCurrencyCode()) {
            return Currency::of($this->currencyOrAttribute);
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

        return Money::ofMinor($value, $this->getCurrency($attributes));
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
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $money = MoneyParser::parse($value, $this->getCurrency($attributes));

        if ($this->currencyOrAttribute) {
            return [
                $key => $money?->getMinorAmount()->toInt(),
                $this->currencyOrAttribute => $money?->getCurrency()->getCurrencyCode(),
            ];
        }

        return [$key => $money?->getMinorAmount()->toInt()];
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
