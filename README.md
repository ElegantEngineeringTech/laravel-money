# Wrapper for Brick/Money

[![Latest Version on Packagist](https://img.shields.io/packagist/v/finller/laravel-money.svg?style=flat-square)](https://packagist.org/packages/finller/laravel-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/finller/laravel-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/finller/laravel-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/finller/laravel-money/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/finller/laravel-money/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/finller/laravel-money.svg?style=flat-square)](https://packagist.org/packages/finller/laravel-money)

Easily use Brick/Money in your laravel App.

## Installation

You can install the package via composer:

```bash
composer require finller/laravel-money
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="money-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
use Finller\MoneyCast;

/**
 * @property ?Money $price
 * @property ?string $currency
 **/
class Invoice extends Model {
    protected $casts = [
        'price' => MoneyCast::class . ':currency'
    ]
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Quentin Gabriele](https://github.com/QuentinGab)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
