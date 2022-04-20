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

### Features supported
- Basic charge
- "Off-session" charge

### Roadmap
- Stripe subscriptions support.

The package rely on the following dependencies:
- `stripe/stripe-php` - Stripe API library.
- `laravel/cashier` - Laravel's helper for Stripe subscriptions, etc.
- `moneyphp/money` - Fowler's Money pattern to store amount/currency data.
- `spatie/data-transfer-object` - Passing data to the provider methods, etc.
- `michael-rubel/laravel-enhanced-container` - Method binding (package extendability & testing)

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

## Installation
Install the package using composer:
```bash
composer require michael-rubel/laravel-stripe-integration
```

Publish the config and fill Stripe keys in `.env`:
```bash
php artisan vendor:publish --tag="stripe-integration-config"
```

## Usage
```php
// Bind contract to the implementation:
bind(PaymentProviderContract::class)->to(StripePaymentProvider::class);

// Resolve bound implementation using the contract:
call(PaymentProviderContract::class)->yourMethod();
```

## Example
```php
// Coming soon...
```

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
