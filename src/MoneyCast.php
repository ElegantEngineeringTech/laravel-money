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
     */
    protected ?string $currency;

    /**
     * @param  string|null  $currency
     */
    public function __construct(?string $currency = null)
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
     * Money is stored as integer and reprensent minor value including decimals
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
     * Money is stored as integer so it include decimals as part the integer (100 = $1.00)
     * But for conveniance and better compatibility with users input we will always consider that the amount is not a minor representation
     * \Brick\Money\Money is cast to string with this format: USD 1000.00 (equivalent to $1,000.00)
     * Given this context, money represented as string are parsed following theses patterns:
     * [money as string] => [common human format]
     * USD 1000.00 => $1,000.00
     * USD 1000 => $1000
     * USD 10.00 => $10.00
     * USD 1,000 => $1000 "," will be ignored
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  null|int|float|string|Money  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value === null || $value === "") {
            return [$key => null];
        }

        if ($value instanceof Money) {
            $money = $value;
        } elseif (is_int($value)) {
            $money = Money::of($value, $this->getMoneyCurrency($attributes), null, RoundingMode::HALF_EVEN);
        } elseif (is_float($value)) {
            $money = Money::of($value, $this->getMoneyCurrency($attributes), null, RoundingMode::HALF_EVEN);
        } elseif (is_string($value)) {
            preg_match("/(?<currency>[A-Z]{3})? ?(?<amount>[\d,\.]*)/", $value, $matches);

            $currencyCode = Arr::get($matches, 'currency');
            $currency = $currencyCode ?
                rescue(fn () => Currency::of($currencyCode), $this->getMoneyCurrency($attributes)) :
                $this->getMoneyCurrency($attributes);

            $amount = str_replace(',', '', Arr::get($matches, 'amount', '0'));

            $money = Money::of($amount, $currency, null, RoundingMode::HALF_EVEN);
        } else {
            throw new Exception(get_class($this) . ' Can not parse value of type: ' . gettype($value));
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
