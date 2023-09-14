<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Laravel\Cashier\Payment;
use MichaelRubel\EnhancedContainer\Core\CallProxy;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentMethodAttachmentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use MichaelRubel\StripeIntegration\Decorators\Contracts\PaymentAmount;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use MichaelRubel\StripeIntegration\Providers\Contracts\PaymentProviderContract;
use MichaelRubel\StripeIntegration\Providers\StripePaymentProvider;
use MichaelRubel\StripeIntegration\Tests\Stubs\User;
use Money\Currency;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentIntentService;

class StripeChargeTest extends TestCase
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var CallProxy
     */
    private CallProxy $paymentProvider;

    /** @setUp */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User;

        $this->be($this->user);

        config(['stripe-integration.secret' => 'sk_test_test']);

        $this->app->bind(PaymentAmount::class, StripePaymentAmount::class);
        $this->app->singleton(PaymentProviderContract::class, StripePaymentProvider::class);

        bind(User::class)->method()->charge(
            fn ($service, $app, $params) => new Payment(
                tap(new PaymentIntent('test_id'), function ($intent) use ($params) {
                    $this->basicCharge($params)->each(fn ($value, $key) => $intent->offsetSet($key, $value));
                })
            )
        );

        bind(User::class)
            ->method()
            ->defaultPaymentMethod(fn () => new PaymentMethod('test_id'));

        bind(PaymentIntentService::class)
            ->method()
            ->create(fn () => new PaymentIntent('test_id'));

        bind(PaymentIntentService::class)
            ->method()
            ->confirm(
                fn ($service, $app, $params) => tap(new PaymentIntent('test_id'), function ($intent) use ($params) {
                    $this->offsessionCharge()->each(fn ($value, $key) => $intent->offsetSet($key, $value));
                    $intent->offsetSet('paymentMethod', $params['params']['paymentMethod']);
                })
            );
    }

    /** @test */
    public function basicUsageChargeTest()
    {
        $cost = app(PaymentAmount::class, [
            PaymentAmount::AMOUNT => 1000,
            PaymentAmount::CURRENCY => new Currency('USD'),
        ]);

        $this->paymentProvider = call(PaymentProviderContract::class);

        $customer = $this->paymentProvider->makeCustomerUsing($this->user);

        $paymentMethod = $this->paymentProvider->setPaymentMethodFor($this->user, 'test_paymentMethod');

        $paymentMethod = $this->paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethodAttachmentData(
                paymentMethod: $paymentMethod,
                customer: $customer,
            )
        );

        $chargeData = new StripeChargeData(
            model: $this->user,
            paymentAmount: $cost,
            paymentMethod: $paymentMethod,
            options: ['description' => 'Test Stripe Charge Description'],
        );

        $payment = $this->paymentProvider->charge($chargeData);

        $this->assertSame('test_id', $paymentMethod->customer);
        $this->assertStringContainsString('succeeded', $payment->status);
        $this->assertStringContainsString('Test Stripe Charge Description', $payment->description);
        $this->assertEquals($cost->getAmount(), $payment->amount);
    }

    /** @test */
    public function offsessionChargeTest()
    {
        $cost = app(PaymentAmount::class, [
            PaymentAmount::AMOUNT => 1000,
            PaymentAmount::CURRENCY => new Currency('USD'),
        ]);

        $this->paymentProvider = call(PaymentProviderContract::class);

        $customer = $this->paymentProvider->makeCustomerUsing($this->user);

        $paymentMethod = $this->paymentProvider->setPaymentMethodFor($this->user, 'test_paymentMethod');

        $this->paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethodAttachmentData(
                paymentMethod: $paymentMethod,
                customer: $customer,
            )
        );

        $chargeData = new OffsessionChargeData(
            model: $this->user,
            paymentAmount: $cost,
            intentParams: ['description' => 'Offsession Charge Description'],
        );

        $payment = $this->paymentProvider->offsessionCharge($chargeData);

        $this->assertSame('succeeded', $payment->status);
        $this->assertSame('test_id', $payment->paymentMethod);
    }

    /** @test */
    public function offsessionChargeWithoutPaymentMethod()
    {
        $cost = app(PaymentAmount::class, [
            PaymentAmount::AMOUNT => 1000,
            PaymentAmount::CURRENCY => new Currency('USD'),
        ]);

        $this->paymentProvider = call(PaymentProviderContract::class);

        bind(User::class)
            ->method()
            ->defaultPaymentMethod(fn () => null);

        $payment = $this->paymentProvider->offsessionCharge(new OffsessionChargeData(
            model: $this->user,
            paymentAmount: $cost,
            intentParams: ['description' => 'Offsession Charge Description'],
        ));

        $this->assertSame('succeeded', $payment->status);
        $this->assertNull($payment->paymentMethod);
    }
}
