![Laravel Stripe Integration](https://user-images.githubusercontent.com/37669560/163988680-2172332f-a735-4429-adc2-7f1bedac130d.png)

# Laravel Stripe Integration
[![Latest Version on Packagist](https://img.shields.io/packagist/v/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/michael-rubel/laravel-stripe-integration)
[![Total Downloads](https://img.shields.io/packagist/dt/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/michael-rubel/laravel-stripe-integration)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/michael-rubel/laravel-stripe-integration/run-tests/main?style=flat-square&label=tests&logo=github)](https://github.com/michael-rubel/laravel-stripe-integration/actions)
[![PHPStan](https://img.shields.io/github/workflow/status/michael-rubel/laravel-stripe-integration/phpstan/main?style=flat-square&label=larastan&logo=laravel)](https://github.com/michael-rubel/laravel-stripe-integration/actions)

This package represents ready to use Stripe Payment Provider class and other related stuff.

The package requires PHP `^8.x` and Laravel `^8.71` or `^9.0`.

Currently, supports only these types of operations:
- Regular Stripe charge;
- Offsession charge;

Under the hood, uses the following dependencies:
- `laravel/cashier` - to easily operate on models and for future improvements
- `spatie/data-transfer-object` - for passing data to the provider methods, etc.
- `michael-rubel/laravel-enhanced-container` - for method binding (package overridability and testing)

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

## Installation
Install the package using composer:
```bash
composer require michael-rubel/laravel-stripe-integration
```

## Usage
```php
// Coming soon...
```

Publish the config:
```bash
php artisan vendor:publish --tag="stripe-integration-config"
```

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
