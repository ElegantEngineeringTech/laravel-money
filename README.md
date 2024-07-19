# Fluent integration of Brick/Money for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/finller/laravel-money.svg?style=flat-square)](https://packagist.org/packages/finller/laravel-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/finller/laravel-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/finller/laravel-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/finller/laravel-money/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/finller/laravel-money/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/finller/laravel-money.svg?style=flat-square)](https://packagist.org/packages/finller/laravel-money)

Easily use Brick/Money in your laravel App.

## Features

-   Cast Model property to `Brick\Money\Money`
-   Parse strings to `Brick\Money\Money`
-   Set of validation rules for money

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
    'locale' => config('app.locale', 'en_US'),
    'default_currency' => 'USD',
];
```

## Usage

### Casting a Model property to a Money instance

You can cast a model property stored as integer to a `Brick\Money\Money` instance.

#### Casting using a column as currency (recommanded)

If you store the currency in a table column alongside the amount value, you can specify the column name like that:

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

#### Casting using a defined currency

You can cast your money to a specific currency using the currency code instead of the the column name.

```php
use Finller\MoneyCast;

/**
 * @property ?Money $price
 * @property ?Money $cost
 **/
class Invoice extends Model {

    protected $casts = [
        'cost' => MoneyCast::class . ':EUR'
        'price' => MoneyCast::class . ':USD'
    ]

}
```

### Parsing a value to a money instance

You can parse any string/int/float to a money instance using `MoneyParser`.

Here are some example of the expected behavior:

```php
use Finller\Money\MoneyParser;

MoneyParser::parse(null, 'EUR') // null

MoneyParser::parse(110, 'EUR') // 110.00€
MoneyParser::parse(100.10, 'EUR') // 100.10€

MoneyParser::parse('', 'EUR') // null
MoneyParser::parse('1', 'EUR') // 1.00€
MoneyParser::parse('100.10', 'EUR') // 100.10€
```

### Validation rules

The package includes some usefull validation rules.

Using `ValidMoney` within Livewire:

```php
namespace App\Livewire;

use Finller\Money\Rules\ValidMoney;
use Illuminate\Foundation\Http\FormRequest;

class CustomComponent extends Component
{
    #[Validate([
        new ValidMoney( nullable: false, min: 0, max: 100)
    ])]
    public ?int $price = null;
}
```

Using `ValidMoney` within a form request:

```php
namespace App\Http\Requests;

use Finller\Money\Rules\ValidMoney;
use Illuminate\Foundation\Http\FormRequest;

class CustomFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'price' => [
                new ValidMoney(
                    nullable: false,
                    min: 0,
                    max: 100
                )
            ],
        ];
    }
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
