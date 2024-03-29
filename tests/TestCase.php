<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Tests;

use Illuminate\Support\Collection;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\StripeIntegrationServiceProvider;
use MichaelRubel\StripeIntegration\Tests\Stubs\User;
use Orchestra\Testbench\TestCase as Orchestra;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentMethodService;
use Stripe\SetupIntent;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        // Set up mocks.
        bind(User::class)->method()->createSetupIntent(function ($service, $app, $params) {
            $intent = new SetupIntent;

            collect($params)->collapse()->each(function ($value, $key) use ($intent) {
                $intent->offsetSet($key, $value);
            });

            return $intent;
        });
        bind(User::class)->method()->createOrGetStripeCustomer(fn () => new Customer('test_id'));

        $paymentMethod = new PaymentMethod('test_id');
        $paymentMethod->offsetSet('customer', null);

        bind(User::class)->method()->updateDefaultPaymentMethod(
            fn () => new CashierPaymentMethod(new User, $paymentMethod)
        );

        bind(PaymentMethodService::class)
            ->method()
            ->attach(function ($service, $app, $params) {
                $intent = new PaymentMethod('test_id');

                collect($params['params'])->each(function ($value, $key) use ($intent) {
                    $intent->offsetSet($key, $value);
                });

                return $intent;
            });
    }

    protected function getPackageProviders($app): array
    {
        return [
            StripeIntegrationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('testing');
    }

    public function basicCharge(array $params): Collection
    {
        return collect([
            'amount' => $params['amount'],
            'description' => $params['options']['description'],
            'payment_method' => $params['paymentMethod'],
            'status' => 'succeeded',
        ]);
    }

    public function offsessionCharge(): Collection
    {
        return collect([
            'status' => 'succeeded',
        ]);
    }
}
