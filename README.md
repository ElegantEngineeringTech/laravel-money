# Elegant Integration of Brick/Money for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elegantly/laravel-money.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elegantly/laravel-money.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-money)

## Table of Contents

-   [Introduction](#introduction)
-   [Features](#features)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Storing Money in the Database](#storing-money-in-the-database)
-   [Usage](#usage)

    -   [Casting with a Currency Column (Recommended)](#casting-with-a-currency-column-recommended)
    -   [Casting with a Fixed Currency](#casting-with-a-fixed-currency)
    -   [Parsing Values](#parsing-values)
    -   [Validation Rule](#validation-rule)

-   [Testing](#testing)
-   [Changelog](#changelog)
-   [Contributing](#contributing)
-   [Security](#security)
-   [Credits](#credits)
-   [License](#license)

## Introduction

This package provides seamless, expressive integration of [Brick/Money](https://github.com/brick/money) with Laravel. It enables safe, precise handling of monetary values in your application—using value objects, smart casting, robust parsing, and powerful validation tools.

## Features

-   **MoneyCast** – Automatically cast Eloquent attributes to `Brick\Money\Money`.
-   **MoneyParser** – Convert strings, integers, or floats into `Money` instances safely.
-   **ValidMoney Rule** – Validate monetary input with min/max boundaries, type safety, and nullability.

## Installation

Install via Composer:

```bash
composer require elegantly/laravel-money
```

## Configuration

Publish the configuration file if you need to customize defaults:

```bash
php artisan vendor:publish --tag="money-config"
```

Default config (`config/money.php`):

```php
return [
    'default_currency' => 'USD',
];
```

## Storing Money in the Database

For maximum precision, store money using:

-   a `bigInteger` column for the amount (in the smallest currency unit)
-   a `string` column for the ISO currency code

This avoids floating-point precision issues and ensures accurate calculations.

Example migration:

```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->bigInteger('amount');   // e.g., 1000 = $10.00
    $table->string('currency', 3);  // ISO 4217 code
    $table->timestamps();
});
```

## Usage

### Casting with a Currency Column (Recommended)

If your model stores both amount and currency, reference the currency column in the cast:

```php
use Elegantly\Money\MoneyCast;
use Brick\Money\Money;

/**
 * @property Money $amount
 * @property string $currency
 */
class Invoice extends Model
{

    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::of('currency'),
        ];
    }
}
```

### Casting with a Fixed Currency

If the currency is known and constant, define it directly:

```php
use Elegantly\Money\MoneyCast;
use Brick\Money\Money;

/**
 * @property Money $amount
 * @property Money $cost
 */
class Invoice extends Model
{
    protected function casts(): array
    {
        return [
            'cost'  => MoneyCast::of('EUR'),
            'price' => MoneyCast::of('USD'),
        ];
    }
}
```

### Parsing Values

`MoneyParser` converts common numeric and string formats into `Money` instances:

```php
use Elegantly\Money\MoneyParser;

MoneyParser::parse(null, 'EUR');     // null
MoneyParser::parse(110, 'EUR');      // 110.00 €
MoneyParser::parse(100.10, 'EUR');   // 100.10 €
MoneyParser::parse('', 'EUR');       // null
MoneyParser::parse('1', 'EUR');      // 1.00 €
MoneyParser::parse('100.10', 'EUR'); // 100.10 €
```

The parser handles nullability, empty strings, integers, floats, and decimal string formats gracefully.

### Validation Rule

#### Using `ValidMoney` in Livewire

```php
namespace App\Livewire;

use Elegantly\Money\Rules\ValidMoney;
use Livewire\Component;

class CustomComponent extends Component
{
    #[Validate([
        new ValidMoney(nullable: false, min: 0, max: 100),
    ])]
    public ?int $price = null;
}
```

#### Using `ValidMoney` in Form Requests

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
                ),
            ],
        ];
    }
}
```

## Testing

Run the test suite:

```bash
composer test
```

## Changelog

See the [CHANGELOG](CHANGELOG.md) for a full history of updates.

## Contributing

Contributions are welcome! Review the [CONTRIBUTING](CONTRIBUTING.md) file for details.

## Security

If you discover a security vulnerability, please refer to the [security policy](../../security/policy).

## Credits

-   [Quentin Gabriele](https://github.com/QuentinGab)
-   [All Contributors](../../contributors)

## License

This package is open-source software released under the MIT License.
See [LICENSE.md](LICENSE.md) for details.
