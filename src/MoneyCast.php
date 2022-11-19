<?php

namespace Finller\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;
use Exception;
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
        if ($this->currency && $modelDefinedCurrency = Arr::get($attributes, $this->currency)) {
            return Currency::of($modelDefinedCurrency);
        }

        return Currency::of(config('money.default_currency'));
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
     * @param  null|int|float|string|Money  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value === null) {
            return [$key => $value];
        }

        if ($value instanceof Money) {
            $money = $value;
        } elseif (is_int($value)) {
            $money = Money::ofMinor($value, $this->getMoneyCurrency($attributes), null, RoundingMode::HALF_EVEN);
        } elseif (is_float($value)) {
            $money = Money::of($value, $this->getMoneyCurrency($attributes), null, RoundingMode::HALF_EVEN);
        } elseif (is_string($value)) {
            $CurrencyCodeGuessed = (string) str($value)->match('/[A-Z]{3}/');
            $currency = rescue(fn () => Currency::of($CurrencyCodeGuessed), $this->getMoneyCurrency($attributes));
            $amount = str($value)->replaceMatches("/[^0-9\.]/", '')->replace([',', '.'], '');
            $money = Money::ofMinor((string) $amount, $currency, null, RoundingMode::HALF_EVEN);
        }else{
            throw new Exception(get_class($this) . " Can not parse value of type: " . gettype($value));
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
