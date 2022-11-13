<?php

namespace Finller\Money\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Finller\Money\Money
 */
class Money extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Finller\Money\Money::class;
    }
}
