<?php

namespace Finller\Money;

use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class MoneyCast implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * The currency code or the model attribute holding the currency code.
     * used like: MoneyCast::class.':currency'
     */
    protected ?string $currency;

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
     * Money is stored as integer and reprensent minor value including decimals
     *
     * @return ?Money
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
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
     * @param  null|int|float|string|Money  $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $money = MoneyParser::parse($value, $this->getMoneyCurrency($attributes));

        return [
            $key => $money?->getMinorAmount()->toInt(),
            $this->currency => $money?->getCurrency()->getCurrencyCode(),
        ];
    }

    /**
     * Get the serialized representation of the value.
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): string
    {
        return (string) $value;
    }
}
