<?php

namespace Elegantly\Money\Tests;

use Brick\Money\Money;
use Elegantly\Money\MoneyCast;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $currency
 * @property Money $price
 * @property Money $price_default_currency
 */
class TestModel extends Model
{
    protected $fillable = ['price', 'currency', 'price_default_currency'];

    protected $casts = [
        'price' => MoneyCast::class.':currency',
        'price_default_currency' => MoneyCast::class,
    ];
}
