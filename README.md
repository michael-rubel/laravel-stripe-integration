![Stripe Charge](https://github.com/michael-rubel/laravel-stripe-integration/assets/37669560/f1767ad2-64ec-414b-9fcb-51c6806765c7)

# Laravel Stripe Integration
[![Latest Version on Packagist](https://img.shields.io/packagist/v/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/michael-rubel/laravel-stripe-integration)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![Infection](https://img.shields.io/github/actions/workflow/status/michael-rubel/laravel-stripe-integration/infection.yml?branch=main&style=flat-square&label=infection&logo=php)](https://github.com/michael-rubel/laravel-stripe-integration/actions)
[![Larastan](https://img.shields.io/github/actions/workflow/status/michael-rubel/laravel-stripe-integration/phpstan.yml?branch=main&style=flat-square&label=larastan&logo=laravel)](https://github.com/michael-rubel/laravel-stripe-integration/actions)

This package is a ready-to-use integration with Stripe. The package uses `laravel/cashier` package internally.

PHP `^8.1` and Laravel `^9.0` is required to use this package.

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

### Features supported
- Basic card charge
- "Off-session" charge
- Payment intent management

## Installation
Install the package using composer:
```bash
composer require michael-rubel/laravel-stripe-integration
```

Publish the config and fill Stripe keys in `.env`:
```bash
php artisan vendor:publish --tag="stripe-integration-config"
```

## Main classes
- [`StripePaymentProvider`](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/src/Providers/StripePaymentProvider.php)
- [`StripePaymentAmount`](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/src/Decorators/StripePaymentAmount.php)

[Usage example](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/docs/usage.md)

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
