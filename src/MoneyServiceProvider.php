<?php

declare(strict_types=1);

namespace Elegantly\Money;

use Brick\Math\RoundingMode;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MoneyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-money')
            ->hasTranslations()
            ->hasConfigFile();
    }

    public static function getDefaultCurrency(): string
    {
        // @phpstan-ignore-next-line
        return config('money.default_currency');
    }

    public static function getRoundingMode(): RoundingMode
    {
        // @phpstan-ignore-next-line
        return config('money.rounding_mode') ?? RoundingMode::HalfUp;
    }
}
