# Elegant Integration of Brick/Money for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elegantly/laravel-money.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-money/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-money/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elegantly/laravel-money.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-money)

## Table of Contents

-   [Introduction](#introduction)
-   [Features](#features)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Storing Money in the Database](#storing-money-in-the-database)
-   [Usage](#usage)
    -   [Casting with a Column as Currency (Recommended)](#casting-with-a-column-as-currency-recommended)
    -   [Casting with a Defined Currency](#casting-with-a-defined-currency)
    -   [Parsing Values to Money Instances](#parsing-values-to-money-instances)
    -   [Validation Rule](#validation-rule)
-   [Testing](#testing)
-   [Changelog](#changelog)
-   [Contributing](#contributing)
-   [Security](#security)
-   [Credits](#credits)
-   [License](#license)

## Introduction

This package provides a seamless integration of [Brick/Money](https://github.com/brick/money) with Laravel, allowing you to handle monetary values efficiently within your application.

## Features

-   **MoneyCast**: Cast model attributes to `Brick\Money\Money`.
-   **MoneyParse**: Convert various data types into `Brick\Money\Money` instances.
-   **ValidMoney**: Implement money validation rules.

## Installation

Install the package via Composer:

```bash
composer require elegantly/laravel-money
```

## Configuration

To customize the default settings, publish the configuration file:

```bash
php artisan vendor:publish --tag="money-config"
```

The default configuration file (`config/money.php`) contains:

```php
return [
    'default_currency' => 'USD',
];
```

## Storing Money in the Database

The recommended way to store money in the database is to use a `bigInteger` column for the amount and a `string` column for the currency. This ensures precision and avoids floating-point errors. Store the amount in the smallest unit of currency (e.g., cents for USD, centimes for EUR). This approach prevents rounding issues and maintains accuracy in calculations.

Example migration:

```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->bigInteger('amount'); // Store in cents
    $table->string('currency', 3); // ISO currency code
    $table->timestamps();
});
```

## Usage

### Casting with a Column as Currency (Recommended)

If your database stores both the amount and the currency in separate columns, you can specify the currency column like this:

```php
use Elegantly\Money\MoneyCast;

/**
 * @property ?Money $amount
 * @property ?string $currency
 **/
class Invoice extends Model {
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => MoneyCast::class . ':currency'
        ];
    }
}
```

### Casting with a Defined Currency

You can also define a specific currency for money casting instead of referencing a column:

```php
use Elegantly\Money\MoneyCast;

/**
 * @property ?Money $price
 * @property ?Money $cost
 **/
class Invoice extends Model {
     /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost' => MoneyCast::class . ':EUR',
            'price' => MoneyCast::class . ':USD'
        ];
    }
}
```

### Parsing Values to Money Instances

Convert strings, integers, or floats into `Brick\Money\Money` instances using `MoneyParser`:

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

#### Using `ValidMoney` in Livewire

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
                )
            ],
        ];
    }
}
```

## Testing

Run the package tests with:

```bash
composer test
```

## Changelog

Refer to the [CHANGELOG](CHANGELOG.md) for details on recent updates and modifications.

## Contributing

Contributions are welcome! See [CONTRIBUTING](CONTRIBUTING.md) for guidelines.

## Security

If you discover any security vulnerabilities, please review our [security policy](../../security/policy) to report them responsibly.

## Credits

-   [Quentin Gabriele](https://github.com/QuentinGab)
-   [All Contributors](../../contributors)

## License

This package is released under the MIT License. See [LICENSE.md](LICENSE.md) for details.
