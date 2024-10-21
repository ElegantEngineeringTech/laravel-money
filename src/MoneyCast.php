<?php

namespace Elegantly\Money;

use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @implements CastsAttributes<null|Money, array<string, int|string>>
 */
class MoneyCast implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * The currency code or the model attribute holding the currency code.
     * When used like: MoneyCast::class.':currency', $currency = 'currency'
     */
    public function __construct(
        protected ?string $currencyOrAttribute = null
    ) {
        //
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function getCurrencyAttribute(array $attributes): ?string
    {
        if ($this->currencyOrAttribute) {
            /** @var ?string $currrency */
            $currrency = Arr::get($attributes, $this->currencyOrAttribute);

            return $currrency;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function getCurrency(array $attributes): Currency
    {
        /** @var string $default */
        $default = config('money.default_currency');

        return Currency::of(
            $this->getCurrencyAttribute($attributes) ?: $default
        );
    }

    /**
     * Cast the given value.
     * Money is stored as integer and reprensent minor value including decimals
     *
     * @param  ?int  $value
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        return Money::ofMinor($value, $this->getCurrency($attributes));
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
     * @return array<string, int|string|null>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $money = MoneyParser::parse($value, $this->getCurrency($attributes));

        if ($money && $this->currencyOrAttribute) {
            return [
                $key => $money->getMinorAmount()->toInt(),
                $this->currencyOrAttribute => $money->getCurrency()->getCurrencyCode(),
            ];
        }

        return [$key => $money?->getMinorAmount()->toInt()];
    }

    /**
     * @param  ?Money  $value
     * @param  array<string, mixed>  $attributes
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        if ($value === null) {
            return $value;
        }

        return $value->getAmount()->toFloat();
    }
}
