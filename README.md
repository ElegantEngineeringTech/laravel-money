# Elegant Integration of Brick/Money for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elegantly/laravel-money.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-money/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-money/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elegantly/laravel-money.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-money)

Easily use Brick/Money in your Laravel app.

## Features

-   MoneyCast: Cast your model attributes to `Brick\Money\Money`
-   MoneyParse: Parse strings and other types to `Brick\Money\Money`
-   ValidMoney: Money validation rule

## Installation

You can install the package via Composer:

```bash
composer require elegantly/laravel-money
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="money-config"
```

This is the content of the published config file:

```php
return [
    'default_currency' => 'USD',
];
```

## Usage

### Casting Using a Column as Currency (Recommended)

If you store the currency in a table column alongside the amount value, you can specify the column name like this:

```php
use Elegantly\MoneyCast;

/**
 * @property ?Money $price
 * @property ?string $currency
 **/
class Invoice extends Model {

    protected $casts = [
        'price' => MoneyCast::class . ':currency'
    ];

}
```

### Casting Using a Defined Currency

You can cast your money to a specific currency using the currency code instead of the column name.

```php
use Elegantly\MoneyCast;

/**
 * @property ?Money $price
 * @property ?Money $cost
 **/
class Invoice extends Model {

    protected $casts = [
        'cost' => MoneyCast::class . ':EUR',
        'price' => MoneyCast::class . ':USD'
    ];

}
```

### Parsing a Value to a Money Instance

You can parse any string/int/float to a money instance using `MoneyParser`.

Here are some examples of the expected behavior:

```php
use Elegantly\Money\MoneyParser;

MoneyParser::parse(null, 'EUR'); // null

MoneyParser::parse(110, 'EUR'); // 110.00€
MoneyParser::parse(100.10, 'EUR'); // 100.10€

MoneyParser::parse('', 'EUR'); // null
MoneyParser::parse('1', 'EUR'); // 1.00€
MoneyParser::parse('100.10', 'EUR'); // 100.10€
```

### Validation Rule

Using `ValidMoney` within Livewire:

```php
namespace App\Livewire;

use Elegantly\Money\Rules\ValidMoney;
use Illuminate\Foundation\Http\FormRequest;

class CustomComponent extends Component
{
    #[Validate([
        new ValidMoney(nullable: false, min: 0, max: 100)
    ])]
    public ?int $price = null;
}
```

Using `ValidMoney` within a form request:

```php
namespace App\Http\Requests;

use Elegantly\Money\Rules\ValidMoney;
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
