# Laravel Stripe Integration
[![Latest Version on Packagist](https://img.shields.io/packagist/v/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/michael-rubel/laravel-stripe-integration)
[![Total Downloads](https://img.shields.io/packagist/dt/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/michael-rubel/laravel-stripe-integration)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/michael-rubel/laravel-stripe-integration/run-tests/main?style=flat-square&label=tests&logo=github)](https://github.com/michael-rubel/laravel-stripe-integration/actions)
[![PHPStan](https://img.shields.io/github/workflow/status/michael-rubel/laravel-stripe-integration/phpstan/main?style=flat-square&label=larastan&logo=laravel)](https://github.com/michael-rubel/laravel-stripe-integration/actions)

This package is ready-to-use integration with Stripe.

The package requires PHP `^8.x` and Laravel `^9.0`.

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

### Features supported
- Basic card charge
- "Off-session" charge
- Payment intent management

### Dependencies
The package rely on the following components:
- [`moneyphp/money`](https://github.com/moneyphp/money)
- [`laravel/cashier`](https://github.com/laravel/cashier-stripe)
- [`spatie/data-transfer-object`](https://github.com/spatie/data-transfer-object)
- [`michael-rubel/laravel-enhanced-container`](https://github.com/michael-rubel/laravel-enhanced-container)

## Installation
Install the package using composer:
```bash
composer require michael-rubel/laravel-stripe-integration
```

Publish the config and fill Stripe keys in `.env`:
```bash
php artisan vendor:publish --tag="stripe-integration-config"
```

## Useful classes
- [`StripePaymentProvider`](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/src/Providers/StripePaymentProvider.php)
- [`StripePaymentAmount`](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/src/Decorators/StripePaymentAmount.php)



[Usage example](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/docs/usage.md)

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
