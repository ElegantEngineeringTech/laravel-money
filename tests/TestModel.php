<?php

declare(strict_types=1);

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
    protected $table = 'tests';

    protected $guarded = [];

    protected $casts = [
        'price' => MoneyCast::class.':currency',
        'price_default_currency' => MoneyCast::class,
    ];
}
