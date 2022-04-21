![Laravel Stripe Integration](https://user-images.githubusercontent.com/37669560/163988680-2172332f-a735-4429-adc2-7f1bedac130d.png)

# Laravel Stripe Integration
[![Latest Version on Packagist](https://img.shields.io/packagist/v/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/michael-rubel/laravel-stripe-integration)
[![Total Downloads](https://img.shields.io/packagist/dt/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/michael-rubel/laravel-stripe-integration)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/michael-rubel/laravel-stripe-integration.svg?style=flat-square&logo=scrutinizer)](https://scrutinizer-ci.com/g/michael-rubel/laravel-stripe-integration/?branch=main)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/michael-rubel/laravel-stripe-integration/run-tests/main?style=flat-square&label=tests&logo=github)](https://github.com/michael-rubel/laravel-stripe-integration/actions)
[![PHPStan](https://img.shields.io/github/workflow/status/michael-rubel/laravel-stripe-integration/phpstan/main?style=flat-square&label=larastan&logo=laravel)](https://github.com/michael-rubel/laravel-stripe-integration/actions)

This package represents ready-to-use integration with Stripe.

The package requires PHP `^8.x` and Laravel `^8.71` or `^9.0`.

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

### Features supported
- Basic card charge
- "Off-session" charge

### Roadmap
- Stripe subscriptions support.

### Dependencies
The package rely on the following components:
- `stripe/stripe-php` - Stripe API library.
- `laravel/cashier` - Laravel's helper for Stripe subscriptions, etc.
- `moneyphp/money` - Fowler's Money pattern to store amount/currency data.
- `spatie/data-transfer-object` - Passing data to the provider methods, etc.
- `michael-rubel/laravel-enhanced-container` - Method binding (package extendability & testing)

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
- [StripePaymentProvider](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/src/Providers/StripePaymentProvider.php)
- [StripePaymentAmount](https://github.com/michael-rubel/laravel-stripe-integration/blob/main/src/Decorators/StripePaymentAmount.php)

## Usage example
```php
/*
|--------------------------------------------------------------------------
| Notes
|--------------------------------------------------------------------------
| Do not just copy & paste the code below. It's only a suggested flow
| definition. You should change the code based on your needs.
| If you wonder what is the `CallProxy`, it is used for
| method binding, i.e. you can use this for mocks.
|
| Check the documentation: https://github.com/michael-rubel/laravel-enhanced-container
*/

class StripeCharge implements Action
{
    /**
     * @var CallProxy
     */
    private CallProxy $paymentProvider;

    /**
     * @param PaymentProviderContract $paymentProvider
     */
    public function __construct(PaymentProviderContract $paymentProvider)
    {
        $this->paymentProvider = call($paymentProvider);
    }

    /**
     * Execute the job.
     *
     * @return mixed
     */
    public function handle(): mixed
    {
        $currency = new Currency('USD');

        $this->paymentProvider->configureCashierCurrency($currency);

        $customer = $this->paymentProvider->prepareCustomer(
            auth()->user()
        );

        $paymentMethod = $this->paymentProvider->updatePaymentMethod(
            auth()->user(),
            'payment_method' // payment_method string from the client library
        );

        $this->paymentProvider->attachPaymentMethodToCustomer(
            $paymentMethod,
            $customer
        );

        $amount = app(PaymentAmount::class, [
            PaymentAmount::AMOUNT   => 1000,
            PaymentAmount::CURRENCY => $currency->getCode(),
        ]);

        $chargeData = new StripeChargeData(
            model: auth()->user(),
            payment_amount: $amount,
            payment_method: $paymentMethod,
            options: ['description' => 'Your description.'],
        );

        $payment = $this->paymentProvider->charge($chargeData);

        // Now you can check $payment->status
    }
}
```

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
