<?php

namespace App\Models\Casts;

use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Support\Arr;

class MoneyCast implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * The currency code or the model attribute holding the currency code.
     * used like: MoneyCast::class.':currency'
     *
     * @var string|null
     */
    protected $currency;

    /**
     * @param  string|null  $currency
     */
    public function __construct(string $currency = null)
    {
        $this->currency = $currency;
    }

    protected function getMoneyCurrency(array $attributes): Currency
    {
        if ($modelDefinedCurrency = Arr::get($attributes, $this->currency)) {
            return Currency::of($modelDefinedCurrency);
        }

        return Currency::of(config('money.defaultCurrency'));
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return ?Money
     */
    public function get($model, $key, $value, $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        return Money::ofMinor($value, $this->getMoneyCurrency($attributes));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  null|string|Money  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value === null) {
            return [$key => $value];
        }

        if ($value instanceof Money) {
            return [
                $key => $value->getMinorAmount()->toInt(),
                $this->currency => $value->getCurrency()->getCurrencyCode(),
            ];
        }

        try {
            $currencyCode = (string) str($value)->match('/[A-Z]{3}/');
            $currency = $currencyCode ? Currency::of($currencyCode) : $this->getMoneyCurrency($attributes);
            $value = (string) str($value)->replaceMatches("/[^0-9\.]/", '')->replace(',', '');
            $money = Money::of($value, $currency);
        } catch (\Throwable $th) {
            throw $th;
        }

        return [
            $key => $money->getMinorAmount()->toInt(),
            $this->currency => $money->getCurrency()->getCurrencyCode(),
        ];
    }

    /**
     * Get the serialized representation of the value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function serialize($model, string $key, $value, array $attributes)
    {
        return (string) $value;
    }
}
